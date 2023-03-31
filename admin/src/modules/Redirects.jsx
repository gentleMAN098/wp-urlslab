import { useState, Suspense, lazy } from 'react';
import { useI18n } from '@wordpress/react-i18n';

// eslint-disable-next-line import/no-extraneous-dependencies
import Overview from '../components/OverviewTemplate';
import RedirectsOverview from '../overview/Redirects';
import ModuleViewHeader from '../components/ModuleViewHeader';

export default function Redirects( { moduleId } ) {
	const { __ } = useI18n();
	const [ activeSection, setActiveSection ] = useState( 'overview' );

	const tableMenu = new Map( [
		[ 'notfound', __( 'Logs' ) ],
	] );

	const SettingsModule = lazy( () => import( `../modules/Settings.jsx` ) );
	const NotFoundTable = lazy( () => import( `../tables/NotFoundTable.jsx` ) );

	return (
		<div className="urlslab-tableView">
			<ModuleViewHeader moduleMenu={ tableMenu } activeMenu={ ( activemenu ) => setActiveSection( activemenu ) } />
			{ activeSection === 'overview' &&
				<Overview moduleId={ moduleId }>
					<RedirectsOverview />
				</Overview>
			}
			{
				activeSection === 'notfound' &&
				<Suspense>
					<NotFoundTable slug={ 'not-found-log' } />
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
