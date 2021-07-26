const gulp = require('gulp')
const webpack = require('webpack-stream')
const webpackConfig = require('./webpack.config')

gulp.task('copy-files', () => {
	gulp.watch('./*').on('change', () => {
		gulp.src([
			'./*',
			'!node_modules{,/**}',
			'!schema.xml',
			'!manifest.xml'
		])
			.pipe(gulp.dest('/home/guido/gitrepos/WorkAssignment/modules/WorkAssignment')) // Adjust to your directory
	})
	gulp.watch('../../Smarty/templates/modules/WorkAssignment/*').on('change', () => {
		gulp.src('../../Smarty/templates/modules/WorkAssignment/*')
			.pipe(gulp.dest('/home/guido/gitrepos/WorkAssignment/templates')) // Adjust to your directory
	})
})

gulp.task('bundle', () => {
	gulp.watch('./src/*.js').on('change', () => {
		gulp.src('./src/*.js')
			.pipe(webpack(webpackConfig))
			.pipe(gulp.dest('./dist'))
	})
})

gulp.task('default', gulp.parallel(['copy-files', 'bundle']))