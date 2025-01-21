import Fuse, { FuseResult } from 'fuse.js';
export type Result = { name: string; id: string };
type DonorList = {
	name: string;
	id: string;
};

type DonorListWithHeaders = {
	name: string;
	id: string;
	headers: [ true | null ];
};

type DonorData =
	| { type: 'noHeaders'; data: DonorList[] }
	| {
			type: 'headers';
			data: { headers: string[]; list: DonorListWithHeaders[] };
	  }
	| { type: 'multiList'; data: { [ key: string ]: DonorList[] } };

export default class Model {
	/**
	 * A lookup of names as name, id (slug) pairs.
	 */
	namesMap: Map< string, string > | Map< string, Map< string, string > >;

	init() {
		const names = window.phfDonorList.donorList as DonorData;
		if ( ! names ) {
			throw new Error( 'Donor List not found' );
		}
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
				names.data.list.map( ( { name, id } ) => [ name, id ] )
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
		const fuse = new Fuse(
			Array.from( db, ( [ name, id ] ) => ( { name, id } ) ),
			{
				keys: [ 'name' ],
				threshold: 0.3,
				includeScore: true,
				minMatchCharLength: 2,
			}
		);
		const results = fuse.search< Result >( name );
		console.log( results );
		if ( results.length > 0 ) {
			return results;
		} else {
			return null;
		}
	}

	private setFuseDb() {
		const values: ( string | Map< string, string > )[] = Array.from(
			this.namesMap.values()
		);
		const isMultiList = values.every( ( value ) => value instanceof Map );
		if ( ! isMultiList ) {
			return this.namesMap as Map< string, string >;
		}
		let db = new Map();
		values.forEach( ( map ) => {
			db = new Map( [ ...db, ...map ] );
		} );
		return db;
	}
}
