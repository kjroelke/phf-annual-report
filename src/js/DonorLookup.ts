import '../styles/utilities/donor-utilities.scss';
import Fuse, { FuseResult } from 'fuse.js';
type Result = { name: string; id: string };

new ( class DonorLookup {
	private input: HTMLInputElement;
	/**
	 * A lookup of names as name, id (slug) pairs.
	 */
	private namesMap: Map< string, string >;
	/**
	 * The container for displaying search results message.
	 */
	private searchResultsMessageContainer: HTMLDivElement;

	private searchForm: HTMLFormElement;

	constructor() {
		try {
			this.namesMap = new Map();
			this.loadNames();
			this.generateForm();
			this.input = document.getElementById(
				'name-lookup'
			) as HTMLInputElement;
			this.searchResultsMessageContainer = document.getElementById(
				'results-details'
			) as HTMLDivElement;
			this.searchForm = document.getElementById(
				'donor-lookup-form'
			) as HTMLFormElement;
			if ( window.location.hash ) {
				const id = window.location.hash.slice( 1 );
				this.highlightDonor( id );
				this.scrollToDonor( id );
				this.generateResetButton();
			} else {
				this.input.focus();
			}
			this.searchForm.addEventListener(
				'submit',
				this.handleLookup.bind( this )
			);
		} catch ( error ) {
			console.error( error );
		}
	}

	private generateForm() {
		const formContainer = document.getElementById( 'form-container' )!;
		const actionUrl = formContainer.getAttribute( 'data-post-slug' );
		formContainer.innerHTML = '';
		formContainer.innerHTML = `<form action="${ window.kjrSiteData.rootUrl }/donors/${ actionUrl }" class="w-100 d-flex flex-wrap gap-3 mb-0" method="get" id="donor-lookup-form">
			<input type="text" name="name" id="name-lookup" class="border border-2 border-primary flex-grow-1" placeholder="Search for your name"/>
			<button type="submit" class="btn btn-primary text-white text-hover--primary">Search</button>
		</form>
		<div id="results-details"></div>`;
	}

	private loadNames() {
		const names = window.phfDonorList.donorList;
		if ( ! names ) {
			throw new Error( 'Donor List not found' );
		}
		names.forEach( ( name ) => {
			this.namesMap.set( name.name, name.id );
		} );
	}

	private handleLookup( event: Event ) {
		event.preventDefault();
		this.generateResetButton();
		const name = this.input.value;
		if ( name === '' ) {
			return;
		}
		const searchResults = this.findDonor( name );
		this.displayResultsMessage( searchResults );
		const ids = searchResults?.map(
			( result ) => this.namesMap.get( result.item.name ) || ''
		);
		if ( ids && ids.length ) {
			ids.forEach( ( id ) => {
				this.highlightDonor( id );
			} );
		}
	}

	/**
	 * Find a donor by name.
	 *
	 * @param name The name of the donor to find.
	 */
	private findDonor( name: string ): FuseResult< Result >[] | null {
		const fuse = new Fuse(
			Array.from( this.namesMap, ( [ name, id ] ) => ( { name, id } ) ),
			{
				keys: [ 'name' ],
				threshold: 0.3,
				includeScore: true,
				minMatchCharLength: 2,
			}
		);
		const results = fuse.search< Result >( name );
		if ( results.length > 0 ) {
			return results;
		} else {
			return null;
		}
	}

	private highlightDonor( id: string ) {
		const donor = document.getElementById( id );
		if ( ! donor ) return;
		const mark = document.createElement( 'mark' );
		donor.parentNode?.insertBefore( mark, donor );
		mark.appendChild( donor );
	}

	private scrollToDonor( id: string ) {
		const donor = document.getElementById( id );
		if ( donor ) {
			const arbitraryOffset = 30;
			window.scrollTo( {
				top: donor.offsetTop - arbitraryOffset,
				behavior: 'smooth',
			} );
		}
	}

	private displayResultsMessage(
		searchResults: FuseResult< Result >[] | null
	) {
		this.searchResultsMessageContainer.innerHTML = '';
		if ( ! searchResults ) {
			const message = document.createElement( 'p' );
			message.textContent = 'No donors found.';
			this.searchResultsMessageContainer.appendChild( message );
			return;
		}
		const message = document.createElement( 'p' );
		message.textContent = 'Found the following donors (click to scroll):';
		this.searchResultsMessageContainer.appendChild( message );
		const list = document.createElement( 'ul' );
		searchResults.forEach( ( result ) => {
			const item = document.createElement( 'li' );
			const anchor = document.createElement( 'a' );
			anchor.href = `#${ result.item.id }`;
			anchor.textContent = result.item.name;
			anchor.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				this.scrollToDonor( result.item.id );
			} );
			item.appendChild( anchor );
			list.appendChild( item );
		} );
		this.searchResultsMessageContainer.appendChild( list );
	}

	private handleReset( ev ) {
		ev.preventDefault();
		let resetList = '';
		for ( const [ name, id ] of this.namesMap.entries() ) {
			resetList += `<li id="${ id }">${ name }</li>`;
		}

		this.searchResultsMessageContainer.innerHTML = '';
		this.input.value = '';
		const results = document.getElementById(
			'donor-list'
		) as HTMLUListElement;
		results.innerHTML = resetList;
		window.location.hash = '';
		const resetButton = this.searchForm.querySelector(
			'button[type="reset"]'
		);
		if ( resetButton ) {
			resetButton.remove();
		}
		this.input.focus();
	}

	private generateResetButton() {
		const reset = this.searchForm.querySelector( 'button[type="reset"]' );
		if ( reset ) {
			return;
		}
		const resetButton = document.createElement( 'button' );
		resetButton.type = 'reset';
		resetButton.classList.add(
			'btn',
			'btn-secondary',
			'text-black',
			'text-hover--secondary'
		);
		resetButton.textContent = 'Reset Search';
		this.searchForm.appendChild( resetButton );
		this.searchForm.addEventListener(
			'reset',
			this.handleReset.bind( this )
		);
	}
} )();
