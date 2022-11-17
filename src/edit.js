/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import {__} from '@wordpress/i18n';
import {Spinner} from '@wordpress/components';
import axios from "axios";
import {dispatch} from '@wordpress/data';

import {useEffect, useReducer} from 'react';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {useBlockProps} from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({attributes, isSelected, setAttributes}) {
	let searchDebounce;

	const blockProps = useBlockProps({
		className: "fnugg-resort",
	});

	const [state, setState] = useReducer(
		(s, a) => ({...s, ...a}),
		{
			isSearching: false,
			isRefreshing: false,
			searchTerm: null,
			resortData: null,
			searchResults: null,
			blockHeading: null,
		}
	);

	const {
		isSearching,
		isRefreshing,
		searchTerm,
		resortData,
		searchResults,
		blockHeading,
	} = state;

	const {
		resortId
	} = attributes;

	const attachSearchEvent = (event) => {
		setState({searchTerm: event.target.textContent});
	};

	const displayNotice = (message) => {
		dispatch('core/notices').createNotice(
			'warning',
			message,
			{
				id: 'fnugg-resort-notice',
				isDismissible: true,
			}
		);
	};

	useEffect(() => {
		if (!resortId || !blockHeading) {
			return;
		}

		setState({isRefreshing: true});

		axios.get(fnuggResort.restBase + '/fnugg-resort/v1/resort/' + resortId)
			.then((response) => {
				blockHeading.textContent = response.data.name;
				setState({resortData: response.data, isRefreshing: false});
			}).catch((response) => {
				displayNotice('Fnugg Resort - Fetch Resort Error: ' + response.data.message);
			});
	}, [blockHeading, resortId]);

	useEffect(() => {
		if (!searchTerm || isSearching) {
			return;
		}

		clearTimeout(searchDebounce);

		searchDebounce = setTimeout(() => {
			setState({isSearching: true});

			axios.get(fnuggResort.restBase + '/fnugg-resort/v1/search&q=' + searchTerm)
				.then((response) => {
					setState({searchResults: response.data, isSearching: false});
				}).catch((response) => {
					setState({isSearching: false});
					displayNotice('Fnugg Resort - Search Error: ' + response.data.message);
				});
		}, 750);
	}, [searchTerm]);

	useEffect(() => {
		setTimeout(() => {
			const blockWrapperId = document.getElementById(blockProps.id);
			const HeadingField = blockWrapperId.getElementsByClassName('fnugg-resort-title')[0];

			if (HeadingField) {
				setState({blockHeading: HeadingField});
				HeadingField.addEventListener('keyup', attachSearchEvent);
			}
		}, 500);
	}, []);

	const selectResort = (event) => {
		setState({searchResults: null});
		setAttributes({resortId: event.target.dataset.resortId});
	}

	return (
		<div {...blockProps}>
			<div className="fnugg-resort-title" placeholder={__('Fnugg Resort (search)', 'fnugg-resort')}
				 contentEditable={true} suppressContentEditableWarning={true}>
				{resortData?.name}
			</div>

			{searchResults &&
				<div className="fnugg-resort-search-results-wrapper">
					<div className="fnugg-resort-search-results">
						{searchResults.map(({id, name}) => {
							return (
								<button key={id} type="button" data-resort-id={id}
										onClick={selectResort}>{name}</button>
							);
						})}
					</div>
				</div>
			}

			<div className="fnugg-resort-content">
				{isRefreshing &&
					<div className="fnugg-resort-refreshing">
						<Spinner/>
					</div>
				}

				<div className="fnugg-resort-image">
					{resortData?.image
						? <img src={resortData.image} alt=""/>
						: <img src={fnuggResort.pluginBaseUrl + '/assets/placeholder.jpg'} alt=""/>
					}

					<div className="fnugg-resort-image-overlay">
						<div className="fnugg-resort-image-overlay-text">
							<div className="fnugg-resort-sub-title">
								{__('Todays conditions', 'fnugg-resort')}
							</div>

							<div className="fnugg-resort-last-update">
								{__('Last updated:', 'fnugg-resort')} {resortData?.lastUpdated}
							</div>
						</div>
					</div>
				</div>

				<div className="fnugg-resort-details">
					<div className="fnugg-resort-weather">
						<img
							src={fnuggResort.pluginBaseUrl + '/assets/icons/resort-weather-blue-' + (resortData?.weather?.symbolId ? resortData.weather.symbolId : 1) + '.svg'}
							alt=""/>
						<div>

						</div>
					</div>
					<div className="fnugg-resort-temperature">
						{resortData?.weather?.temperature?.degrees}°
					</div>

					<div className="fnugg-resort-wind">
						<div>
							<img
								src={fnuggResort.pluginBaseUrl + '/assets/icons/wind-direction.svg'}
								alt=""/>
							<span
								className="fnugg-resort-wind-speed">{resortData?.weather?.wind?.speed}</span>
							<span className="fnugg-resort-wind-unit">m/s</span>
						</div>

						<div>
							&mdash;
						</div>
					</div>
					<div className="fnugg-resort-conditions">
						<img
							src={fnuggResort.pluginBaseUrl + '/assets/icons/slope.svg'}
							alt=""/>
						{resortData?.weather?.description}

					</div>
				</div>
			</div>
		</div>
	);
}
