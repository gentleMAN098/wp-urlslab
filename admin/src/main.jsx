/* eslint-disable import/no-extraneous-dependencies */
import React from 'react';
import { createRoot } from 'react-dom';

import {
	QueryClient,
} from '@tanstack/react-query';
import {
	PersistQueryClientProvider } from '@tanstack/react-query-persist-client';
import { idbPersister } from './constants/queryClientPersister';

import App from './App';

const queryClient = new QueryClient( {
	defaultOptions: {
		queries: {
			cacheTime: 1000 * 60 * 60 * 24, // 24 hours
		},
	},
} );

createRoot( document.getElementById( 'urlslab-root' ) ).render(
	<React.StrictMode>
		<PersistQueryClientProvider client={ queryClient } persistOptions={ { persister: idbPersister } }>
			<App />
		</PersistQueryClientProvider>
	</React.StrictMode>
);
