<?php
namespace LDX\PeacefulSpawn;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
class Main extends PluginBase implements Listener {
  public function onLoad() {
    $this->getLogger()->info(TextFormat::YELLOW . "Loading PeacefulSpawn v2.0 by LDX...");
  }
  public function onEnable() {
    $this->enabled = true;
    $this->getLogger()->info(TextFormat::YELLOW . "Enabling PeacefulSpawn...");
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    
  }
  public function onCommand(CommandSender $issuer,Command $cmd,$label,array $args) {
    if(strtolower($cmd->getName()) == "ps") {
      $this->enabled = !$this->enabled;
      if($this->enabled) {
        $issuer->sendMessage("[PeacefulSpawn] Spawn protection enabled!");
        $this->getLogger()->info(TextFormat::YELLOW . "Spawn protection enabled!");
      } else {
        $issuer->sendMessage("[PeacefulSpawn] Spawn protection disabled!");
        $this->getLogger()->info(TextFormat::YELLOW . "Spawn protection disabled!");
      }
      return true;
    } else {
      return false;
    }
  }
  /**
  * @param EntityDamageEvent $event
  *
  * @priority HIGHEST
  * @ignoreCancelled false
  */
  public function onHurt(EntityDamageEvent $event) {
    $entity = $event->getEntity();
    $v = new Vector3($entity->getLevel()->getSpawn()->x,$entity->y,$entity->getLevel()->getSpawn()->z);
    $r = $this->getServer()->getSpawnRadius();
    if($entity instanceof Player && $entity->distance($v) <= $r && $this->enabled == true) {
      $event->setCancelled();
    }
  }
  public function onDisable() {
    $this->getLogger()->info(TextFormat::YELLOW . "Disabling PeacefulSpawn...");
  }
}
?>
