/**
 * External dependencies
 */
import { WC_BLOCKS_IMAGE_URL } from '@woocommerce/block-settings';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { useProductDataContext } from '@woocommerce/shared-context';

/**
 * Internal dependencies
 */
import largeImageNextPreviousButtonMetadata from '../product-gallery-next-previous-buttons/block.json';

const getInnerBlocksTemplate = () => [
	[ largeImageNextPreviousButtonMetadata.name ],
];

type ProductImageProps = {
	image: { src: string; alt: string };
	cropImages: boolean;
};

const ProductImage = ( { image, cropImages }: ProductImageProps ) => {
	const placeholderSrc = `${ WC_BLOCKS_IMAGE_URL }block-placeholders/product-image-gallery.svg`;

	const src = image.src || placeholderSrc;
	const alt = image.alt || '';

	return (
		<div className="wc-block-product-gallery-large-image wc-block-editor-product-gallery-large-image">
			<img
				src={ src }
				alt={ alt }
				loading="lazy"
				className={
					cropImages
						? 'wc-block-woocommerce-product-gallery-large-image__image--cropped'
						: ''
				}
			/>
		</div>
	);
};

type ProductGalleryLargeImageProps = {
	context: { cropImages?: boolean };
};

export const Edit = ( { context }: ProductGalleryLargeImageProps ) => {
	const productContext = useProductDataContext();
	const firstImage = productContext?.product?.images?.[ 0 ];
	const image = {
		src: firstImage?.src || '',
		alt: firstImage?.alt || '',
	};

	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'wc-block-product-gallery-large-image__inner-blocks',
		},
		{
			template: getInnerBlocksTemplate(),
			templateInsertUpdatesSelection: true,
		}
	);

	const blockProps = useBlockProps( {
		className:
			'wc-block-product-gallery-large-image wc-block-editor-product-gallery-large-image',
	} );

	return (
		<div { ...blockProps }>
			<ProductImage
				image={ image }
				cropImages={ context.cropImages ?? false }
			/>
			<div { ...innerBlocksProps } />
		</div>
	);
};
