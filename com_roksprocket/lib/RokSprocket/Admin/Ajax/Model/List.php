<?php
/**
 * @version   $Id: List.php 57545 2012-10-14 22:51:08Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocketAdminAjaxModelList extends RokCommon_Ajax_AbstractModel
{
	/**
	 * Delete the file and all associated rows (done by foreign keys) and files
	 * $params object should be a json like
	 * <code>
	 * {
	 *  "ids":[ 3, 4, 5]
	 * }
	 * </code>
	 *
	 * @param $params
	 *
	 * @return RokCommon_Ajax_Result
	 * @throws Exception
	 */
	public function delete($params)
	{
		$result = new RokCommon_Ajax_Result();
		try {
			$container = RokCommon_Service::getContainer();
			/** @var $platforminfo RokCommon_IPlatformInfo */
			$platforminfo = $container->getService('platforminfo');
			/** @var $model RokSprocket_Model_List */
			$model = $container->getService('roksprocket.list.model');
			foreach ($params->ids as $id) if ($model->delete($id) === false) {
				throw new Exception($model->getLastError());
			}
			$result->setPayload(array('redirect' => $platforminfo->getRootUrl() . '/wp-admin/admin.php?page=roksprocket-list'));
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}
}
