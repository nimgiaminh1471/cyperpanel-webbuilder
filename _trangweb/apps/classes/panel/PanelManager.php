<?php
/**
 * Factory: trả về adapter DirectAdmin hoặc CyberPanel theo CONFIG["BUILDER"]["panel"].
 */
namespace classes\panel;

final class PanelManager {

	private static $instance = null;

	public static function get(): PanelManagerInterface {
		if (self::$instance === null) {
			$panel = strtolower(CONFIG["BUILDER"]["panel"] ?? "directadmin");
			self::$instance = $panel === "cyberpanel" ? new CyberPanelManager() : new DirectAdminPanel();
		}
		return self::$instance;
	}
}
