import '../styles/layouts/donors.scss';
import DOMHandler from './donor-lists/DOMHandler';
import Model from './donor-lists/Model';
new ( class DonorLookup {
	private view: DOMHandler;
	private db: Model;

	constructor() {
		this.db = new Model();

		try {
			this.db.init();
			this.view = new DOMHandler(
				this.db.findDonor.bind( this.db ),
				this.db.namesMap,
				this.db.dbType
			);
			this.view.init();
		} catch ( error ) {
			console.error( error );
		}
	}
} )();
