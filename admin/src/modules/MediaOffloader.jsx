import { useState, Suspense, lazy } from 'react';
import { useI18n } from '@wordpress/react-i18n';

import TableViewHeader from '../components/TableViewHeader';

export default function MediaOffloader( { moduleId } ) {
	const { __ } = useI18n();
	const [ activeSection, setActiveSection ] = useState( 'overview' );

	const tableMenu = new Map( [
		[ 'file', __( 'Media Files Table' ) ],
	] );

	const SettingsModule = lazy( () => import( `../modules/Settings.jsx` ) );
	const MediaFilesTable = lazy( () => import( `../tables/MediaFilesTable.jsx` ) );

	return (
		<div className="urlslab-tableView">
			<TableViewHeader tableMenu={ tableMenu } activeMenu={ ( activemenu ) => setActiveSection( activemenu ) } />
			{
				activeSection === 'file' &&
				<Suspense>
					<MediaFilesTable />
				</Suspense>
			}
			{
				activeSection === 'settings' &&
				<Suspense>
					<SettingsModule className="fadeInto" settingId={ moduleId } />
				</Suspense>
			}
		</div>
	);
}
