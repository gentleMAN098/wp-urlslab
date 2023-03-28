import { useRef } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { useI18n } from '@wordpress/react-i18n';

import { setData } from '../api/fetching';
import useChangeRow from '../hooks/useChangeRow';
import useCloseModal from '../hooks/useCloseModal';
import Button from '../elements/Button';

export default function InsertRowPanel( { insertOptions, handlePanel } ) {
	const queryClient = useQueryClient();
	const { __ } = useI18n();
	const enableAddButton = useRef( false );
	const { CloseIcon, handleClose } = useCloseModal( handlePanel );

	const { inserterCells, title, text, data, slug, url, pageId, rowToInsert } = insertOptions;
	const flattenedData = data?.pages?.flatMap( ( page ) => page ?? [] );
	const { insertRowResult, insertRow } = useChangeRow( { data: flattenedData, url, slug, pageId } );
	const requiredFields = Object.keys( inserterCells ).filter( ( cell ) => inserterCells[ cell ].props.required === true );

	// Checking if all required fields are filled in rowToInsert object
	if ( requiredFields.every( ( key ) => Object.keys( rowToInsert ).includes( key ) ) ) {
		enableAddButton.current = true;
	}

	const hidePanel = ( operation ) => {
		handleClose();
		if ( handlePanel ) {
			handlePanel( operation );
		}
	};

	if ( insertRowResult?.ok ) {
		setTimeout( () => {
			hidePanel();
		}, 100 );
	}
	return (
		<div className="urlslab-panel-wrap urlslab-panel-floating fadeInto">
			<div className="urlslab-panel">
				<div className="urlslab-panel-header">
					<h3>{ title }</h3>
					<button className="urlslab-panel-close" onClick={ hidePanel }>
						<CloseIcon />
					</button>
					<p>{ text }</p>
				</div>
				<div className="mt-l">
					{
						Object.entries( inserterCells ).map( ( [ cellId, cell ] ) => {
							return <div className="mb-l" key={ cellId }>
								{ cell }
							</div>;
						} )
					}
					<div className="flex">
						<Button className="ma-left simple" onClick={ hidePanel }>{ __( 'Cancel' ) }</Button>
						<Button active disabled={ ! enableAddButton.current } onClick={ () => insertRow( { rowToInsert } ) }>{ title }</Button>
					</div>
				</div>
			</div>
		</div>
	);
}
