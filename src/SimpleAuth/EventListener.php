<?php

/*
 * SimpleAuth plugin for PocketMine-MP
 * Copyright (C) 2014 PocketMine Team <https://github.com/PocketMine/SimpleAuth>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

declare(strict_types=1);

namespace SimpleAuth;


use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Server;

class EventListener implements Listener{
	/** @var SimpleAuth */
	private $plugin;
	private $perms;

	public function __construct(SimpleAuth $plugin){
		$this->plugin = $plugin;
	}



	/**
	 * @param DataPacketReceiveEvent $event
	 *
	 * @priority LOWEST
	 */

	public function onDataPacketReceive(DataPacketReceiveEvent $event){
		if($event->getPacket() instanceof LoginPacket && $event->getPacket()->username !== null){
			if($this->plugin->getConfig()->get("allowLinking")){
				$linkedPlayerName = $this->plugin->getDataProvider()->getLinked($event->getPacket()->username);
				if($linkedPlayerName !== null && $linkedPlayerName !== ""){
					$pmdata = $this->plugin->getDataProvider()->getPlayerData($linkedPlayerName);
					if($pmdata !== null){
						$player = $event->getPlayer();
						$player->namedtag = Server::getInstance()->getOfflinePlayerData($linkedPlayerName);
						$event->getPacket()->username = $linkedPlayerName;
					}
				}
			}
			$this->plugin->devices[$event->getPacket()->username] = $event->getPacket()->clientData["DeviceModel"];
		}
	}

	/**
	 * @param PlayerCommandPreprocessEvent $event
	 *
	 * @priority MONITOR
	 */
	public function onPlayerCommand(PlayerCommandPreprocessEvent $event){
		if(!$this->plugin->isPlayerAuthenticated($event->getPlayer())){

		}
	}


}
