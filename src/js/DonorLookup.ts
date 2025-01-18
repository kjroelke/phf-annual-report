import '../styles/utilities/donor-utilities.scss';
new ( class DonorLookup {
	private input: HTMLInputElement;
	/**
	 * A lookup of names as name, id (slug) pairs.
	 */
	private namesMap: Map< string, string >;

	constructor() {
		this.input = document.getElementById(
			'name-lookup'
		) as HTMLInputElement;
		this.namesMap = new Map();
		this.loadNames();
		const form = document.getElementById(
			'donor-lookup-form'
		) as HTMLFormElement;
		form.addEventListener( 'submit', this.handleLookup.bind( this ) );
	}

	private loadNames() {
		const donorList = document.getElementById(
			'donor-list'
		) as HTMLUListElement;
		const names = donorList.querySelectorAll( 'li' );
		names.forEach( ( name ) => {
			const nameText = name.innerText.trim();
			if ( nameText ) {
				this.namesMap.set( nameText, name.id );
			}
		} );
	}

	private handleLookup( event: Event ) {
		event.preventDefault();
		const name = this.input.value;
		if ( name === '' ) {
			return;
		}
		const id = this.namesMap.get( name );
		if ( id ) {
			const donor = document.getElementById( id );
			if ( donor ) {
				const mark = document.createElement( 'mark' );
				donor.parentNode?.insertBefore( mark, donor );
				mark.appendChild( donor );
				const arbitraryOffset = 30;
				window.scrollTo( {
					top: donor.offsetTop - arbitraryOffset,
					behavior: 'smooth',
				} );
			}
		}
	}
} )();
