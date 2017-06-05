<?php
/*
Plugin Name: Thumbnail Category
Description: add thumbnail to the category taxonomy
Author: Ihsan Berahim
Version: 1.0
Author URI: https://github.com/ihsanberahim
*/

add_action('category_add_form_fields', 'add_category_group_field', 10);
add_action('category_edit_form_fields', 'edit_category_group_field', 10, 2);

add_action('edited_category', 'update_category_meta', 10, 2);
add_action('created_category', 'save_category_meta', 10, 2);

add_action('admin_enqueue_scripts', 'ibctLoadMediaUploader');

function ibctRenderCategoryThumbnailJs() {
?>
<script>
var ibctMediaUploader, ibctMedia;
function doSelectCategoryThumbnail() {
 ibctMediaUploader = wp.media.frames.file_frame = wp.media({
         frame:    'post',
         state:    'insert',
         multiple: false
     });
 ibctMediaUploader.on('insert', function()
 {
  json = ibctMediaUploader.state().get( 'selection' ).first().toJSON();

  if (json.url.indexOf('http')===-1) {
   return;
  }

  jQuery('#category_thumbnail').val(json.url);
 });
 ibctMediaUploader.open();

	return false;
}
</script>
<?php
}

function ibctRenderEditCategoryThumbnailField($term){
	$category_thumbnail = get_term_meta($term->term_id, 'category_thumbnail', true);
?>
<tr class="form-field term-group-wrap">
	<th scope="row">
		<label for="category_thumbnail">Thumbnail
		</label>
	</th>
	<td>
		<input class="postform" id="category_thumbnail" name="category_thumbnail" type="text" onclick="doSelectCategoryThumbnail()" value="<?php echo $category_thumbnail; ?>"/>
  <p class="description">click to select and set photo</p>
	</td>
</tr>
<?php
}

function ibctRenderCategoryThumbnailField(){
?>
<div class="form-field term-group">
    <label for="category_thumbnail">Thumbnail</label>
    <input class="postform" id="category_thumbnail" name="category_thumbnail" type="text" onclick="doSelectCategoryThumbnail()"/>
    <p class="description">click to select and set photo</p>
</div>
<?php
}

function add_category_group_field() {
	ibctRenderCategoryThumbnailField();
	ibctRenderCategoryThumbnailJs();
}

function edit_category_group_field($term, $taxonomy) {
	ibctRenderEditCategoryThumbnailField($term);
	ibctRenderCategoryThumbnailJs();
}

function save_category_meta($term_id, $tt_id){
    if(isset( $_POST['category_thumbnail']) && '' !== $_POST['category_thumbnail']){
      $group = esc_sql($_POST['category_thumbnail']);
      add_term_meta($term_id, 'category_thumbnail', $group, true);
    }
}

function update_category_meta($term_id, $tt_id){
    if(isset($_POST['category_thumbnail']) && '' !== $_POST['category_thumbnail']){
        $group = esc_sql($_POST['category_thumbnail']);
        update_term_meta($term_id, 'category_thumbnail', $group);
    }
}

function ibctLoadMediaUploader(){
	global $pagenow;

	if($pagenow!=='edit-tags.php'
		&& $pagenow!=='term.php') return;

 wp_enqueue_media();
}
