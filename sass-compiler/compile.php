<?php 
/*
 * Compile and generate style.css and style.min.css from sass file style.less
 */
function sass_compile_less_mincss(){
	include( dirname( __FILE__ ) . '/compile_less_sass_class.php' );
	
	$less_file      = dirname( __FILE__ ) . '/assets/css/style.less';
	$css_file       = dirname( __FILE__ ) . '/assets/css/style.css';
	$css_min_file       = dirname( __FILE__ ) . '/assets/css/style.min.css';
	
	$compile = new Compile_Less_Sass;
	$compile->compileLessFile( $less_file, $css_file, $css_min_file );
}
sass_compile_less_mincss();