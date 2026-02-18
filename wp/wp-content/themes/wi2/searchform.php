<?php $currentlang = get_bloginfo('language'); ?>
<form id="searchform" class="form-inline" role="search" method="get" action="<?php bloginfo('url'); ?>/">
    <div class="form-group">
	<input type="search" class="searchform" placeholder="Wyszukaj..." value="<?php the_search_query(); ?>" name="s" id="s" />
    </div>
</form>