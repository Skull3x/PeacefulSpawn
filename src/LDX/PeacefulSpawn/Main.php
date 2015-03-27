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
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
class Main extends PluginBase implements Listener {
	
	public $enabled = array();
	
  public function onEnable() {
	@mkdir($this->getDataFolder());
	new Config($this->getDataFolder() . "config.yml", CONFIG::YAML);
	if($this->getConfig()->getNested("Worlds.world") == null) {
		$this->getConfig()->setNested("Worlds.world",[
		20,
	]);
	$this->getConfig()->save();
	}
	$this->getConfig()->save();
	$w = $this->getConfig()->get("Worlds");
	$worlds = $this->getServer()->getLevels();
	foreach ($worlds as $w) {
		$this->enabled[$w->getName()] = true;
	}
	$this->toggle = true;
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
	
  }
  public function onCommand(CommandSender $issuer,Command $cmd,$label,array $args) {
    if(strtolower($cmd->getName()) == "ps" ) {
		if(isset($args[0])) {
			if($this->getServer()->getLevelByName($args[0])) {
				if($issuer->hasPermission("peacefulspawn") || $issuer->hasPermission("peacefulspawn.toggle")) {
					if(!isset($this->enabled[$args[0]])) {
						$this->enabled[$args[0]] = true;
					}else{
						unset($this->enabled[$args[0]]);
					}
					if(isset($this->enabled[$args[0]])) {
					  $issuer->sendMessage("[PeacefulSpawn] Spawn protection enabled for $args[0]'s spawn!");
					  $this->getLogger()->info(TextFormat::YELLOW . "Spawn protection enabled for $args[0]'s spawn!");
					} else {
					  $issuer->sendMessage("[PeacefulSpawn] Spawn protection disabled for $args[0]'s spawn!");
					  $this->getLogger()->info(TextFormat::YELLOW . "Spawn protection disabled for $args[0]'s spawn!");
					}
				} else {
					$issuer->sendMessage("You do not have permission to toggle spawn protection.");
				}
			} else {
				$issuer->sendMessage("[PeacefulSpawn] World not found!");
			}
		} else {
			$this->toggle = !$this->toggle;
			if($this->toggle) {
			  $issuer->sendMessage("[PeacefulSpawn] Spawn protection enabled for all world spawns!");
			  $this->getLogger()->info(TextFormat::YELLOW . "Spawn protection enabled for all world spawns!");
			} else {
			  $issuer->sendMessage("[PeacefulSpawn] Spawn protection disabled for all world spawns!");
			  $this->getLogger()->info(TextFormat::YELLOW . "Spawn protection disabled for all world spawns!");
			}
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
  public function onHurt(EntityDamageEvent $event) {
    $entity = $event->getEntity();
    $v = new Vector3($entity->getLevel()->getSpawnLocation()->getX(),$entity->getPosition()->getY(),$entity->getLevel()->getSpawnLocation()->getZ());
	$w = $entity->getLevel()->getName();
    $r = $this->getConfig()->getNested("Worlds.$w");
	if($r == null || $r == 0) {
		return true;
	}
    if(($entity instanceof Player) && ($entity->getPosition()->distance($v) <= $r[0]) && ($this->toggle) && (isset($this->enabled[$w]))) {
      $event->setCancelled();
    }
  }
   /**
  * @param EntityDamageByEntityEvent $event
  *
  * @priority HIGHEST
  * @ignoreCancelled true
  */
  public function onHurtByEntity(EntityDamageByEntityEvent $event) {
    $entity = $event->getEntity();
    $v = new Vector3($entity->getLevel()->getSpawnLocation()->getX(),$entity->getPosition()->getY(),$entity->getLevel()->getSpawnLocation()->getZ());
	$w = $entity->getLevel()->getName();
    $r = $this->getConfig()->getNested("Worlds.$w");
	if($r == null || $r == 0) {
		return true;
	}
    if(($entity instanceof Player) && ($entity->getPosition()->distance($v) <= $r[0]) && ($this->toggle) && (isset($this->enabled[$w]))) {
      $event->setCancelled();
    }
  }
}
?>
