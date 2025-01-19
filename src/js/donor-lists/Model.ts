import Fuse, { FuseResult } from 'fuse.js';
export type Result = { name: string; id: string };

export default class Model {
	/**
	 * A lookup of names as name, id (slug) pairs.
	 */
	namesMap: Map< string, string > | Map< string, Map< string, string > >;

	init() {
		const names = window.phfDonorList.donorList as
			| Array< { name: string; id: string } >
			| { [ key: string ]: Array< { name: string; id: string } > };
		if ( ! names ) {
			throw new Error( 'Donor List not found' );
		}
		if ( Array.isArray( names ) ) {
			this.namesMap = new Map(
				names.map( ( { name, id } ) => [ id, name ] )
			);
		} else {
			this.namesMap = new Map(
				Object.entries( names ).map( ( [ key, value ] ) => [
					`donor-list-${ key }`,
					new Map( value.map( ( { id, name } ) => [ name, id ] ) ),
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
