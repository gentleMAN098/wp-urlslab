import { useState, Suspense, lazy } from 'react';
import Overview from '../components/OverviewTemplate';
import ModuleViewHeader from '../components/ModuleViewHeader';
import CacheOverview from "../overview/Cache";
import {useI18n} from "@wordpress/react-i18n";

export default function Cache( { moduleId } ) {
	const { __ } = useI18n();
	const [ activeSection, setActiveSection ] = useState( 'overview' );
	const tableMenu = new Map( [
		[ 'cache-rules', __( 'Cache Rules' ) ],
	] );

	const SettingsModule = lazy( () => import( `../modules/Settings.jsx` ) );
	const CacheRulesTable = lazy( () => import( `../tables/CacheRulesTable.jsx` ) );

	return (
		<div className="urlslab-tableView">
			<ModuleViewHeader moduleMenu={ tableMenu } activeMenu={ ( activemenu ) => setActiveSection( activemenu ) } />
			{
				activeSection === 'overview' &&
				<Overview moduleId={ moduleId }>
					<CacheOverview />
				</Overview>
			}
			{
				activeSection === 'cache-rules' &&
				<Suspense>
					<CacheRulesTable slug={ 'cache-rules' } />
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
