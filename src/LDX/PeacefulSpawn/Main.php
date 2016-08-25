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

class Main extends PluginBase implements Listener {
  public function onEnable() {
    $this->enabled = true;
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  public function onCommand(CommandSender $issuer, Command $cmd, $label, array $args) {
    if(strtolower($cmd->getName()) == "ps" ) {
      if($issuer->hasPermission("peacefulspawn") || $issuer->hasPermission("peacefulspawn.toggle")) {
        $this->enabled = !$this->enabled;
        if($this->enabled) {
          $issuer->sendMessage("Spawn security enabled!");
          $this->getLogger()->info(TextFormat::YELLOW . "Spawn security enabled!");
        } else {
          $issuer->sendMessage("Spawn security disabled!");
          $this->getLogger()->info(TextFormat::YELLOW . "Spawn security disabled!");
        }
      } else {
        $issuer->sendMessage("You do not have permission to toggle spawn protection.");
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
  * @ignoreCancelled true
  */
  public function onHurt(\pocketmine\event\entity\EntityDamageEvent $event) {
    $entity = $event->getEntity();
    $v = new Vector3($entity->getLevel()->getSpawnLocation()->getX(),$entity->getPosition()->getY(),$entity->getLevel()->getSpawnLocation()->getZ());
    $r = $this->getServer()->getSpawnRadius();
    if(($entity instanceof \pocketmine\Player) && ($entity->getPosition()->distance($v) <= $r) && ($this->enabled == true)) {
      $event->setCancelled(true);
    }
  }
  
  public function onPrime(\pocketmine\event\entity\ExplosionPrimeEvent $event) {
    $entity = $event->getEntity();
    $v = new Vector3($entity->getLevel()->getSpawnLocation()->getX(),$entity->getPosition()->getY(),$entity->getLevel()->getSpawnLocation()->getZ());
    $r = $this->getServer()->getSpawnRadius();
    if(($entity instanceof \pocketmine\entity\PrimedTNT) && ($entity->getPosition()->distance($v) <= $r) && ($this->enabled == true)) {
      $event->setCancelled(true);
    }
  }
}
