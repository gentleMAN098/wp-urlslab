import {
	useInfiniteFetch, ProgressBar, SortBy, Tooltip, SortMenu, InputField, Checkbox, Trash, Loader, Table, ModuleViewHeaderBottom, TooltipSortingFiltering,
} from '../lib/tableImports';

import useTableUpdater from '../hooks/useTableUpdater';
import useChangeRow from '../hooks/useChangeRow';

export default function SearchReplaceTable( { slug } ) {
	const paginationId = 'id';

	const { table, setTable, rowToInsert, setInsertRow, filters, setFilters, sorting, sortBy } = useTableUpdater( { slug } );

	const url = `${ 'undefined' === typeof filters ? '' : filters }${ 'undefined' === typeof sorting ? '' : sorting }`;

	const {
		__,
		columnHelper,
		data,
		status,
		isSuccess,
		isFetching,
		isFetchingNextPage,
		hasNextPage,
		ref,
	} = useInfiniteFetch( { key: slug, filters, sorting, paginationId } );

	const { row, selectedRows, selectRow, deleteRow, deleteSelectedRows, updateRow } = useChangeRow( { data, url, slug, paginationId } );

	const searchTypes = {
		T: __( 'Plain text' ),
		R: __( 'Regular expression' ),
	};

	const header = {
		str_search: __( 'Search string (old)' ),
		str_replace: __( 'Replace string (new)' ),
		search_type: __( 'Search type' ),
		url_filter: 'URL filter',
	};

	const inserterCells = {
		str_search: <InputField liveUpdate type="url" defaultValue="" label={ header.str_search } onChange={ ( val ) => setInsertRow( { ...rowToInsert, str_search: val } ) } required />,
		str_replace: <InputField liveUpdate type="url" defaultValue="" label={ header.str_replace } onChange={ ( val ) => setInsertRow( { ...rowToInsert, str_replace: val } ) } required />,
		search_type: <SortMenu autoClose items={ searchTypes } name="search_type" checkedId="T" onChange={ ( val ) => setInsertRow( { ...rowToInsert, search_type: val } ) }>{ header.search_type }</SortMenu>,
		url_filter: <InputField liveUpdate defaultValue="" label={ header.url_filter } onChange={ ( val ) => setInsertRow( { ...rowToInsert, url_filter: val } ) } />,
	};

	const columns = [
		columnHelper.accessor( 'check', {
			className: 'nolimit checkbox',
			cell: ( cell ) => <Checkbox checked={ cell.row.getIsSelected() } onChange={ ( val ) => {
				selectRow( val, cell );
			} } />,
			header: null,
		} ),
		columnHelper.accessor( 'str_search', {
			className: 'nolimit',
			cell: ( cell ) => <InputField type="text" defaultValue={ cell.getValue() } onChange={ ( newVal ) => updateRow( { newVal, cell } ) } />,
			header: <SortBy props={ { header, sorting, key: 'str_search', onClick: () => sortBy( 'str_search' ) } }>{ header.str_search }</SortBy>,
			size: 300,
		} ),
		columnHelper.accessor( 'str_replace', {
			className: 'nolimit',
			cell: ( cell ) => <InputField type="text" defaultValue={ cell.getValue() } onChange={ ( newVal ) => updateRow( { newVal, cell } ) } />,
			header: <SortBy props={ { header, sorting, key: 'str_replace', onClick: () => sortBy( 'str_replace' ) } }>{ header.str_replace }</SortBy>,
			size: 300,
		} ),
		columnHelper.accessor( 'search_type', {
			filterValMenu: searchTypes,
			className: 'nolimit',
			cell: ( cell ) => <SortMenu items={ searchTypes } name={ cell.column.id } checkedId={ cell.getValue() } onChange={ ( newVal ) => updateRow( { newVal, cell } ) } />,
			header: <SortBy props={ { header, sorting, key: 'search_type', onClick: () => sortBy( 'search_type' ) } }>{ header.search_type }</SortBy>,
			size: 100,
		} ),
		columnHelper.accessor( 'url_filter', {
			className: 'nolimit',
			cell: ( cell ) => <InputField type="text" defaultValue={ cell.getValue() } onChange={ ( newVal ) => updateRow( { newVal, cell } ) } />,
			header: <SortBy props={ { header, sorting, key: 'url_filter', onClick: () => sortBy( 'url_filter' ) } }>{ header.url_filter }</SortBy>,
			size: 100,
		} ),
		columnHelper.accessor( 'delete', {
			className: 'deleteRow',
			tooltip: () => <Tooltip className="align-left xxxl">{ __( 'Delete item' ) }</Tooltip>,
			cell: ( cell ) => <Trash onClick={ () => deleteRow( { cell } ) } />,
			header: () => null,
		} ),
	];

	if ( status === 'loading' ) {
		return <Loader />;
	}

	return (
		<>
			<ModuleViewHeaderBottom
				slug={ slug }
				header={ header }
				table={ table }
				selectedRows={ selectedRows }
				onSort={ ( val ) => sortBy( val ) }
				onDeleteSelected={ deleteSelectedRows }
				onFilter={ ( filter ) => setFilters( filter ) }
				onClearRow={ ( clear ) => {
					setInsertRow();
					if ( clear === 'rowInserted' ) {
						setInsertRow( clear );
						setTimeout( () => {
							setInsertRow();
						}, 3000 );
					}
				} }
				insertOptions={ { inserterCells, title: 'Add replacement', data, slug, url, paginationId, rowToInsert } }
				exportOptions={ {
					slug,
					filters,
					fromId: `from_${ paginationId }`,
					paginationId,
					deleteCSVCols: [ paginationId, 'dest_url_id' ],
				} }
			/>
			<Table className="fadeInto"
				slug={ slug }
				returnTable={ ( returnTable ) => setTable( returnTable ) }
				columns={ columns }
				data={ isSuccess && data?.pages?.flatMap( ( page ) => page ?? [] ) }
			>
				{ row
					? <Tooltip center>{ `${ header.str_search } “${ row.str_search }”` } { __( 'has been deleted.' ) }</Tooltip>
					: null
				}
				{ ( rowToInsert === 'rowInserted' )
					? <Tooltip center>{ __( 'Search & Replace rule has been added.' ) }</Tooltip>
					: null
				}
				<TooltipSortingFiltering props={ { isFetching, filters, sorting } } />
				<div ref={ ref }>
					{ isFetchingNextPage ? '' : hasNextPage }
					<ProgressBar className="infiniteScroll" value={ ! isFetchingNextPage ? 0 : 100 } />
				</div>
			</Table>
		</>
	);
}
