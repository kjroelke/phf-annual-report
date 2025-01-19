import { FuseResult } from 'fuse.js';
import { Result } from './Model';

export default class DOMHandler {
	input: HTMLInputElement;
	/**
	 * The container for displaying search results message.
	 */
	private searchResultsMessageContainer: HTMLDivElement;

	private searchForm: HTMLFormElement;
	private findDonor: ( name: string ) => FuseResult< Result >[] | null;
	private namesMap:
		| Map< string, string >
		| Map< string, Map< string, string > >;

	constructor(
		findDonor: ( name: string ) => FuseResult< Result >[] | null,
		namesMap: Map< string, string > | Map< string, Map< string, string > >
	) {
		this.findDonor = findDonor;
		this.namesMap = namesMap;
	}
	init() {
		this.generateForm();
		this.initClassProperties();
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
	}

	/**
	 * Generate the form for donor lookup.
	 */
	private generateForm() {
		const formContainer = document.getElementById( 'form-container' )!;
		const actionUrl = formContainer.getAttribute( 'data-post-slug' );
		formContainer.innerHTML = '';
		formContainer.innerHTML = `<form action="${ window.kjrSiteData.rootUrl }/donors/${ actionUrl }" class="w-100 d-flex flex-wrap gap-3 mb-0" method="get" id="donor-lookup-form">
			<input type="text" name="name" id="name-lookup" class="border border-2 border-primary flex-grow-1" placeholder="Enter a name"/>
			<button type="submit" class="btn btn-primary text-white text-hover--primary">Search</button>
		</form>
		<div id="results-details"></div>`;
	}

	/**
	 * Wires up class properties after the form has been created
	 */
	private initClassProperties() {
		this.input = document.getElementById(
			'name-lookup'
		) as HTMLInputElement;
		this.searchResultsMessageContainer = document.getElementById(
			'results-details'
		) as HTMLDivElement;
		this.searchForm = document.getElementById(
			'donor-lookup-form'
		) as HTMLFormElement;
	}

	private handleLookup( event: Event ) {
		event.preventDefault();
		this.generateResetButton();
		const name = this.input.value;
		if ( name === '' ) {
			return;
		}
		this.displayResults( this.findDonor( name ) );
	}

	private displayResults( searchResults: FuseResult< Result >[] | null ) {
		this.searchResultsMessageContainer.innerHTML = '';
		const message = document.createElement( 'p' );
		message.textContent = searchResults
			? 'Found the following donors (click to scroll):'
			: 'No donors found.';
		this.searchResultsMessageContainer.appendChild( message );
		if ( searchResults ) {
			this.appendResultsList( searchResults );
		}
	}

	/**
	 * Appends the search results list to the search results message container.
	 * @param searchResults The search results to display.
	 */
	private appendResultsList( searchResults: FuseResult< Result >[] ): void {
		const list = document.createElement( 'ul' );
		searchResults.forEach( ( result ) => {
			const item = this.createListItem( result.item );
			list.appendChild( item );
		} );
		this.searchResultsMessageContainer.appendChild( list );
	}

	private createListItem( result: {
		id: string;
		name: string;
	} ): HTMLLIElement {
		const { id, name } = result;
		const item = document.createElement( 'li' );
		const anchor = document.createElement( 'a' );
		anchor.href = `#${ id }`;
		anchor.textContent = name;
		anchor.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			this.highlightDonor( id );
			this.scrollToDonor( id );
		} );
		item.appendChild( anchor );
		return item;
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
		this.searchForm.addEventListener( 'reset', ( ev ) =>
			this.handleReset( ev )
		);
	}

	/**
	 * Resets the search form and search results to their intial state.
	 * @param ev The event object.
	 */
	private handleReset( ev: Event ) {
		ev.preventDefault();
		window.location.hash = '';
		this.resetFormAndFormContainer();
		const values: ( string | Map< string, string > )[] = Array.from(
			this.namesMap.values()
		);
		const isMultiList = values.every( ( value ) => value instanceof Map );
		if ( ! isMultiList ) {
			this.resetResults();
		} else {
			for ( const [ key, value ] of this.namesMap.entries() ) {
				this.resetResults( key, value );
			}
		}
	}

	private resetResults( id?: string, map?: Map< string, string > ) {
		const selector = id ?? 'donor-list';
		const results = document.getElementById( selector ) as HTMLUListElement;
		let resetList = '';
		const namesMap = map ?? this.namesMap;
		for ( const [ name, id ] of namesMap.entries() ) {
			resetList += `<li id="${ id }">${ name }</li>`;
		}
		results.innerHTML = resetList;
	}

	private resetFormAndFormContainer() {
		this.searchResultsMessageContainer.innerHTML = '';
		const resetButton = this.searchForm.querySelector(
			'button[type="reset"]'
		);
		if ( resetButton ) {
			resetButton.remove();
		}
		this.input.value = '';
		this.input.focus();
	}
}
