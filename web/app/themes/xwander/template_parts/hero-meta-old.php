<?php 
global $post;


$duration_suffix = get_field('tour_duration_suffix', $post->ID ); 
$duration = get_field('tour_duration', $post->ID ).' '. __($duration_suffix, 'xwander');
$level = get_field('tour_level', $post->ID );
$level_class = $level;
$level = __($level, 'xwander');

$level_values_fi = array()
 ?>
	
<div class="single-tour-meta">
	
	<?php if ($duration): ?>
	<div class="tour-duration"><?php echo $duration; ?></div>
	<?php endif; ?>
		                

	<?php if ($level): ?>		                	
	<div class="tour-level-diagram" >
		
		<div class="level-diagram tour-difficulty active-level-<?php echo $level_class; ?>">
				<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="signal" class="svg-inline--fa fa-signal fa-w-20 " role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M216 288h-48c-8.84 0-16 7.16-16 16v192c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V304c0-8.84-7.16-16-16-16zM88 384H40c-8.84 0-16 7.16-16 16v96c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16v-96c0-8.84-7.16-16-16-16zm256-192h-48c-8.84 0-16 7.16-16 16v288c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V208c0-8.84-7.16-16-16-16zm128-96h-48c-8.84 0-16 7.16-16 16v384c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V112c0-8.84-7.16-16-16-16zM600 0h-48c-8.84 0-16 7.16-16 16v480c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V16c0-8.84-7.16-16-16-16z"></path>
			</svg>
		</div>

		
		<div class="level-diagram passive-level">
			<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="signal" class="svg-inline--fa fa-signal fa-w-20 " role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M216 288h-48c-8.84 0-16 7.16-16 16v192c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V304c0-8.84-7.16-16-16-16zM88 384H40c-8.84 0-16 7.16-16 16v96c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16v-96c0-8.84-7.16-16-16-16zm256-192h-48c-8.84 0-16 7.16-16 16v288c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V208c0-8.84-7.16-16-16-16zm128-96h-48c-8.84 0-16 7.16-16 16v384c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V112c0-8.84-7.16-16-16-16zM600 0h-48c-8.84 0-16 7.16-16 16v480c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V16c0-8.84-7.16-16-16-16z"></path>
			</svg>
		</div>
		
		<span class="level-title"><?php echo $level; ?></span>
	</div>
	<?php endif; ?>

</div>
                
                 

