let mix = require('laravel-mix');

mix
	.sass('wp-content/themes/cutaway/sass/theme.scss', 'wp-content/themes/cutaway/css/')
	//.sass('wp-content/themes/spedidam/sass/custom-editor-style.scss', 'wp-content/themes/spedidam/css/')
	.options({processCssUrls: false})
	.setPublicPath('./')
	.sourceMaps();
