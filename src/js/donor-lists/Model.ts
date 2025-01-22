import Fuse, { FuseResult } from 'fuse.js';
export type Result = { name: string; id: string };
type DonorList = {
	name: string;
	id: string;
};

export type MultiColumnDonorList = {
	name: string;
	id: string;
	headers: [ true | null ][];
};

type DonorData =
	| { type: 'noHeaders'; data: DonorList[] }
	| {
			type: 'multiColumn';
			data: { headers: string[]; list: MultiColumnDonorList[] };
	  }
	| { type: 'multiList'; data: { [ key: string ]: DonorList[] } };

export default class Model {
	/**
	 * A lookup of names as name, id (slug) pairs.
	 */
	namesMap:
		| Map< string, string >
		| Map< string, Map< string, string > >
		| Map< string, { id: string; headers: [ true | null ] } >;
	dbType: 'noHeaders' | 'multiColumn' | 'multiList';

	init() {
		const names = window.phfDonorList.donorList as DonorData;
		if ( ! names ) {
			throw new Error( 'Donor List not found' );
		}
		this.dbType = names.type;
		if ( 'noHeaders' === names.type ) {
			this.namesMap = new Map(
				names.data.map( ( { name, id } ) => [ name, id ] )
			);
		} else if ( 'multiList' === names.type ) {
			this.namesMap = new Map(
				Object.entries( names.data ).map( ( [ key, value ] ) => [
					`donor-list-${ key }`,
					new Map( value.map( ( { id, name } ) => [ name, id ] ) ),
				] )
			);
		} else {
			this.namesMap = new Map(
				names.data.list.map( ( { name, id, headers } ) => [
					name,
					{ id, headers },
				] )
			);
		}
	}

	/**
	 * Find a donor by name.
	 *
	 * @param name The name of the donor to find.
	 */
	findDonor( name: string ): FuseResult< Result >[] | null {
		const db = this.setFuseDb();
		const fuse = new Fuse( db, {
			keys: [ 'name' ],
			threshold: 0.3,
			includeScore: true,
			minMatchCharLength: 2,
		} );
		const results = fuse.search< Result >( name );
		if ( results.length > 0 ) {
			return results;
		} else {
			return null;
		}
	}

	private setFuseDb() {
		if ( 'noHeaders' === this.dbType ) {
			return Array.from(
				this.namesMap as Map< string, string >,
				( [ name, id ] ) => ( { name, id } )
			);
		}
		let db = new Map();
		if ( 'multiList' === this.dbType ) {
			const values: ( string | Map< string, string > )[] = Array.from(
				this.namesMap.values()
			);
			values.forEach( ( map ) => {
				db = new Map( [ ...db, ...map ] );
			} );
			return Array.from( db, ( [ name, id ] ) => ( { name, id } ) );
		} else {
			for ( const [ key, value ] of this.namesMap.entries() ) {
				let values = value as MultiColumnDonorList;
				db.set( key, {
					id: values.id,
					headers: values.headers,
					name: key,
				} );
			}
			return Array.from( db.entries(), ( [ name, value ] ) => ( {
				name,
				value,
				id: value.id,
			} ) );
		}
	}
}
