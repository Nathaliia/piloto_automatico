<?php 
/**
 * @package Profiler for Joomla! 3.0
 * @version $Id: default.php 13 2013-04-26 15:59:36Z harold $
 * @author Harold Prins
 * @copyright (C) 2011-2013 Harold Prins
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 *  
 * Profiler is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *  
 * You should have received a copy of the GNU General Public License
 * along with Profiler.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
*/
defined('_JEXEC') or die;


?>
<div id="ajaxdefault" class="container-fluid">

	<?php //ajax tabel ?>
	
</div>

<script type="text/javascript">

	onload = function()
	{
		ajaxdisplay();
		
		
	}



	

	function ajaxdisplay() {
		var url = "<?php echo JRoute::_(ProfilerHelperRoute::getUsersajaxdefaultRoute(), false); ?>";
		var postform = jQuery("#filter_search").serialize();

		jQuery.post(url,	postform)
			.done(function(result) {
				jQuery( "#ajaxdefault" ).empty().append( result );
				ajaxsearch();
		});

	}
	
	
	function ajaxsearch() {
		var url = "<?php echo JRoute::_(ProfilerHelperRoute::getUsersajaxRoute(), false); ?>";
		var postform = jQuery("#filter_search").serialize();

		jQuery.post(url,	postform)
			.done(function(result) {
				jQuery( "#ajaxuserlist" ).empty().append( result );
		});

	}



</script>

 
