<?php
/*
 *  Copyright (C) 2012 sateffen
 *  https://github.com/sateffen/ShowNewMessagesInMenu-for-Bengine
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class ShowNewMessagesInMenu extends Recipe_PluginAbstract
{

	public function __construct()
	{
		$this->pluginName = "Show new Messages in Menü";
		$this->pluginVersion = "1.0";
		return;
	}

	protected function countNewMessages()
	{
		$select = array("f.folder_id", "f.label", "f.is_standard", "COUNT(m.msgid) AS messages", "SUM(m.read) AS `read`", "SUM(LENGTH(m.message)) AS `storage`");
		$joins = "LEFT JOIN ".PREFIX."message m ON (m.mode = f.folder_id AND m.receiver = '".Core::getUser()->get("userid")."')";
		$where = "f.userid = '".Core::getUser()->get("userid")."' OR f.is_standard = '1'";
		$result = Core::getQuery()->select("folder f", $select, $joins, $where, "", "", "f.folder_id");
		
		$unreadMessages = 0;
		
		while($row = Core::getDB()->fetch($result))
		{
			$unreadMessages += ( $row["messages"] - (int) $row["read"] );
		}
		
		return $unreadMessages;
	}

	public function onHtmlEnd( $element )
	{
		$unreadMessages = $this->countNewMessages();
		if( $unreadMessages > 0 )
		{
			return '<script type="text/javascript">
//<![CDATA[
$("#leftMenu").find("[href$=\'/MSG\']").append(" ('. $unreadMessages .')");
//]]>
</script>';
		}
		return NULL;
	}
}

Hook::addHook( "HtmlEnd" , new ShowNewMessagesInMenu() );

?>
