const gulp = require('gulp')

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

gulp.task('default', gulp.parallel('copy-files'))