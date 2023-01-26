import { lazy, Suspense } from 'react';
import ErrorBoundary from './ErrorBoundary';
import Loader from './Loader';
import '../assets/styles/layouts/_DynamicModule.scss';

export default function DynamicModule( { modules, moduleId, onChange } ) {
	const handleModuleValues = ( module, value ) => {
		if ( onChange ) {
			onChange( module, value );
		}
	};

	/* Renames module id from ie urlslab-lazy-loading to LazyLoading
    Always capitalize first character in FileName.jsx after - when creating component/module !!!
    so urlslab-lazy-loading becomes LazyLoading.jsx component
  */
	const renameModule = () => {
		const name = moduleId.replace( 'urlslab', '' );
		return name.replace( /-(\w)/g, ( char ) => char.replace( '-', '' ).toUpperCase() );
	};

	const Module = lazy( () => import( `../modules/${ renameModule() }.jsx` ) );

	return (
		<div className="urlslab-DynamicModule">
			<ErrorBoundary>
				<Suspense fallback={ <Loader /> }>
					<Module modules={ modules } moduleId={ moduleId } onChange={ ( module, value ) => handleModuleValues( module, value ) } />
				</Suspense>
			</ErrorBoundary>
		</div>
	);
}
