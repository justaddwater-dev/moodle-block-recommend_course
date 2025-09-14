<ul class="block_recommend_course-tabs">
    <li class="tab-button <?php if(basename($_SERVER['PHP_SELF'])=='history.php') echo 'active'; ?>" data-tab="tab5">
        <a href="<?php echo new moodle_url('/blocks/recommend_course/history.php') ?>"><?php echo get_string('historytitle', 'block_recommend_course'); ?></a>
    </li>
    <li class="tab-button <?php if(basename($_SERVER['PHP_SELF'])=='stats_table.php') echo 'active'; ?>" data-tab="tab5">
        <a href="<?php echo new moodle_url('/blocks/recommend_course/stats_table.php') ?>"><?php echo get_string('course_recommendations_stats', 'block_recommend_course'); ?></a>
        
    </li>
</ul>