import { useState, useCallback, useEffect } from 'react';
import { useI18n } from '@wordpress/react-i18n';
import { get } from 'idb-keyval';

import SimpleButton from '../elements/SimpleButton';

import '../assets/styles/components/_ModuleViewHeader.scss';

export default function ModuleViewHeader( { moduleId, moduleMenu, activeMenu, noSettings } ) {
	const { __ } = useI18n();
	const [ active, setActive ] = useState( 'overview' );

	const menuItems = new Map( [
		[ 'overview', __( 'Overview' ) ],
		[ 'settings', __( 'Settings' ) ],
	] );

	const handleMenu = ( menukey ) => {
		setActive( menukey );
		if ( activeMenu ) {
			activeMenu( menukey );
		}
	};

	const activator = ( menukey ) => {
		if ( menukey === active ) {
			return 'active';
		}
		return '';
	};

	const getOverviewVisibility = useCallback( async () => {
		const moduleData = moduleId && await get( moduleId );

		if ( moduleData?.hideOverview && moduleMenu ) {
			handleMenu( Array.from( moduleMenu )[ 0 ][ 0 ] );
		}
	}, [ ] );

	useEffect( () => {
		getOverviewVisibility();
	}, [ ] );

	return (

		<div className="urlslab-moduleView-header">
			<div className="urlslab-moduleView-headerTop">
				<SimpleButton key={ 'overview' }
					className={ activator( 'overview' ) }
					onClick={ () => handleMenu( 'overview' ) }
				>
					{ menuItems.get( 'overview' ) }
				</SimpleButton>
				{ moduleMenu
					? Array.from( moduleMenu ).map( ( [ key, value ] ) => {
						return <SimpleButton key={ key }
							className={ activator( key ) }
							onClick={ () => handleMenu( key ) }
						>
							{ value }
						</SimpleButton>;
					} )
					: null
				}
				{ ! noSettings &&
					<SimpleButton key={ 'settings' }
						className={ activator( 'settings' ) }
						onClick={ () => handleMenu( 'settings' ) }
					>
						{ menuItems.get( 'settings' ) }
					</SimpleButton>
				}
			</div>
		</div>
	);
}
