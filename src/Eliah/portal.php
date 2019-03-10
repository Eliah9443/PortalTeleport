<?php

namespace Eliah;

use pocketmine\plugin\PluginBase;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\block\utils\InvalidBlockStateException;
use pocketmine\Player;
use pocketmine\level\Location;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\MovingObjectPosition;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\metadata\Metadatable;
use pocketmine\metadata\MetadataValue;

class portal extends PluginBase implements Listener {

    public $prefix = "§8[§5PortalTeleport§8] §f";

    public $config;

    public $message;

    public function onEnable() {
        $this->getLogger()->info($this->prefix."The plugin was loaded!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->saveResource("message.yml");
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $this->message = new Config($this->getDataFolder()."message.yml", Config::YAML);
        $this->saveDefaultConfig();
        $this->getLogger()->notice($this->prefix."Plugin is made by Eliah.");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
      if($cmd == "setend") {
        $messages = $this->message->get("endpoint_message");
        $this->config->set("PortalLevel", $sender->getLevel()->getFolderName());
        $this->config->setNested("PortalXYZ", array (
        'x' => $sender->getFloorX(),
        'y' => $sender->getFloorY(),
        'z' => $sender->getFloorZ()
      ));
      $this->config->save();

      $sender->sendMessage($this->prefix.$messages);

      }
      return false;
    }

  	public function onMovement(PlayerMoveEvent $event) {
      $messages = $this->message->get("teleport_message");
      $player = $event->getPlayer();
      $block = $player->getLevel()->getBlock($player->floor()->add(0, +1));
      $id = $block->getId();
      $x = $this->config->getNested("PortalXYZ.x");
      $y = $this->config->getNested("PortalXYZ.y");
      $z = $this->config->getNested("PoratlXYZ.z");
      $level = $this->getServer()->getLevelByName($this->config->get("PortalLevel"));
      if($id == Block::PORTAL) {
        $player->sendMessage($this->prefix.$messages);
        $player->teleport(new Position($x, $y, $z, $level));

  }
  return false;
}

    public function onJoin(PlayerJoinEvent $event) {
      $player = $event->getPlayer();
      if($player->isOp()) {
        $player->sendMessage($this->prefix."Plugin made by Eliah. §9Discord: §fEliah#7620 §bTwitter: §f@EliahJs §7GitHub: §fEliah9443");
      }
    }

    public function onDisable() {
      $messages = $this->message->get("plugin_disable");
      $this->getLogger()->alert($this->prefix.$messages);
    }

}
