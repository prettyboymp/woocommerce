/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

const Edit = () => {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<p>Hello World</p>
		</div>
	);
};

export default Edit;
