const path = require( 'path' );
const fs = require( 'fs' );
const glob = require( 'glob' );

function blockSupportsInteractivity( blockJson ) {
	if ( typeof blockJson?.supports?.interactivity === 'object' ) {
		return blockJson.supports.interactivity?.interactive === true;
	}

	return blockJson?.supports?.interactivity === true;
}

function findInteractivityBlockAssets( dir, additionalPatterns = [] ) {
	let results = [];
	const ents = fs.readdirSync( dir, { withFileTypes: true } );

	for ( const entry of ents ) {
		const fullPath = path.join( dir, entry.name );
		if ( entry.isDirectory() ) {
			results = results.concat(
				findInteractivityBlockAssets( fullPath, additionalPatterns )
			);
		} else if ( entry.isFile() && entry.name === 'block.json' ) {
			// parse the json file and determine if its a block that supports interactivity.
			const blockJson = JSON.parse( fs.readFileSync( fullPath, 'utf8' ) );

			if ( blockSupportsInteractivity( blockJson ) ) {
				const blockDir = path.dirname( fullPath );
				const assets = additionalPatterns.flatMap( ( pattern ) =>
					glob.sync( pattern, { cwd: blockDir, absolute: true } )
				);
				results.push( {
					blockName: blockJson.name,
					blockJson: fullPath,
					assets,
				} );
			}
		}
	}

	return results;
}

const interactivityBlocks = findInteractivityBlockAssets(
	path.resolve( __dirname, '../assets/js' ),
	[ 'frontend.*s', 'style.scss', 'editor.scss' ]
);

const scriptModuleEntries = interactivityBlocks.reduce( ( acc, block ) => {
	const frontendFile = block.assets.find( ( f ) => f.includes( 'frontend' ) );
	if ( frontendFile ) {
		acc[ block.blockName ] = frontendFile;
	}
	return acc;
}, {} );

const styleEntries = interactivityBlocks.reduce( ( acc, block ) => {
	const styleFile = block.assets.find( ( f ) => f.includes( 'style' ) );
	if ( styleFile ) {
		acc[ `${ block.blockName }-style` ] = styleFile;
	}
	return acc;
}, {} );

const editorStyleEntries = interactivityBlocks.reduce( ( acc, block ) => {
	const editorFile = block.assets.find( ( f ) => f.includes( 'editor' ) );
	if ( editorFile ) {
		acc[ `${ block.blockName }-editor` ] = editorFile;
	}
	return acc;
}, {} );

module.exports = {
	scriptModuleEntries,
	styleEntries,
	editorStyleEntries,
};
