/* eslint-disable react-hooks/exhaustive-deps */
import { useEffect, useState, useRef, useMemo } from 'react';
import { useI18n } from '@wordpress/react-i18n';
import Button from './Button';
import Checkbox from './Checkbox';
import { ReactComponent as ColumnsIcon } from '../assets/images/icon-columns.svg';

import '../assets/styles/elements/_FilterMenu.scss';
import '../assets/styles/elements/_Checkbox.scss';

export default function ColumnsMenu( {
	id, className, table, items, style } ) {
	const { __ } = useI18n();
	const [ isActive, setActive ] = useState( false );
	const [ isVisible, setVisible ] = useState( false );
	const [ checked, setChecked ] = useState( Object.keys( items ) );
	const ref = useRef( id );

	useEffect( ( ) => {
		const handleClickOutside = ( event ) => {
			if ( ! ref.current?.contains( event.target ) && isActive && ref.current?.id === id ) {
				setActive( false );
				setVisible( false );
			}
		};
		document.addEventListener( 'click', handleClickOutside, false );
	}, [ id, isActive ] );

	const checkedCheckbox = ( column, isChecked ) => {
		column.toggleVisibility();
		if ( isChecked ) {
			const checkedList = [ ...checked, column.id ];
			// checkedNow = [ ... new Set( checkedList ) ];
			setChecked( [ ... new Set( checkedList ) ] );
		}
		if ( ! isChecked ) {
			// checkedNow = checked.filter( ( item ) => item !== column.id );
			setChecked( checked.filter( ( item ) => item !== column.id ) );
		}
	};

	const handleMenu = () => {
		setActive( ! isActive );

		setTimeout( () => {
			setVisible( ! isVisible );
		}, 100 );
	};

	return (
		<div className={ `urlslab-FilterMenu urlslab-ColumnsMenu ${ className || '' } ${ isActive ? 'active' : '' }` } style={ style } ref={ ref } id={ id }>
			<div
				className={ `urlslab-ColumnsMenu__title ${ isActive ? 'active' : '' }` }
				onClick={ handleMenu }
				onKeyUp={ ( event ) => handleMenu( event ) }
				role="button"
				tabIndex={ 0 }
			>
				<ColumnsIcon />
			</div>
			<div className={ `urlslab-FilterMenu__items ${ isActive ? 'active' : '' } ${ isVisible ? 'visible' : '' }` }>
				<div className={ `urlslab-FilterMenu__items--inn ${ items.length > 8 ? 'has-scrollbar' : '' }` }>
					<Button className="small"
						onClick={ () => table.toggleAllColumnsVisible() }
					>
						Toggle All
					</Button>
					{ table?.getAllLeafColumns().map( ( column ) => {
						return (
							items[ column.id ] &&
							<Checkbox
								className="urlslab-FilterMenu__item"
								key={ column.id }
								id={ column.id }
								onChange={ ( isChecked ) => checkedCheckbox( column, isChecked ) }
								checked={ checked.includes( column.id ) }
							>
								{ items[ column.id ] }
							</Checkbox>
						);
					} ) }

				</div>
			</div>
		</div>
	);
}
