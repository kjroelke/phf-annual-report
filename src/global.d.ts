export {};

declare global {
	interface Window {
		phfDonorList: {
			donorList: Array< { name: string; id: string } >;
		};
		kjrSiteData: {
			rootUrl: string;
		};
	}
}
