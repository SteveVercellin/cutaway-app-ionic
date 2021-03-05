<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<script>

	jQuery(document).ready(function(){

		jQuery('#hide_menu_admin_form li span.show_submenu').click(function(){

			jQuery(this).parent('li').find('.submenu').toggle('fast');

		});

	});

</script>
<h2>Remove admin menus by role</h2>
<form action="" method="post" id="hide_menu_admin_form">
	<?php wp_nonce_field( 'rambr' ) ?>
	<div class="column">
		<strong>For these roles :</strong>
		<ul>
		<?php
			foreach (get_editable_roles() as $role_name => $role_infos)
			{
				if($role_name != 'administrator')
					echo '<li><input type="checkbox" name="roles[]" value="'.$role_name.'" '.(in_array($role_name, $roles_selected) ? 'checked="checked"' : '').' />'.$role_name.'</li>';
			}
		?>
		</ul>
	</div>
	<div class="column">
		<strong>Remove these menus :</strong>
		<ul class="menus">
<?php

	foreach($menu as $k => $m)
	{
		if($m[0])
		{
			echo '<li class="menu">
			  <input type="checkbox" name="menus_hidden[]" value="'.$m[2].'" '.(in_array($m[2], $menus_hidden) ? 'checked="checked"' : '').' />'.$m[0];

			//submenus ?
			if(@sizeof($submenu[$m[2]]) > 0)
			{
				echo '<span class="show_submenu"></span><div class="submenu">';
				foreach($submenu[$m[2]] as $sm)
					echo '<input type="checkbox" name="submenus_hidden[]" value="'.$m[2].'|'.$sm[2].'" '.(in_array($m[2].'|'.$sm[2], $submenus_hidden) ? 'checked="checked"' : '').' />'.$sm[0].'<br />';
				echo '</div>';
			}

			echo '</li>';
		}
		
	}
?>

</ul></div>
<input type="submit" value="Save profile" />
</form>

<p>
	You need multiple profiles ? Look at <a href="http://www.info-d-74.com/produit/remove-admin-menus-by-role-pro-plugin-wordpress/" target="_blank">Pro version <a href="https://www.facebook.com/infod74/" target="_blank"><img src="<?php echo plugins_url( 'images/fb.png', dirname(__FILE__)) ?>" alt="" /></a><br />
		<img src="<?= plugins_url( 'images/pro_version.png', dirname(__FILE__) ); ?>" />
	</a>
</p>