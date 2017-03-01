<?php

/*
 * Code for grabbing file contents
 * file_get_contents("http://x.com/file.txt");
 */
namespace majorplayz;

/*
 * Majorcraft Core Plugin
 *  
 *  
 * Written By:
 * >> MajorPlayz, VCraftPE and Muqsit
 */
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class Main extends PluginBase implements Listener {
	// Constnants:
	const AUTHOR = "MajorPlayz";
	const PREFIX = "Core:";
	
	public function onEnable() {
		$this->getLogger ()->info ( TextFormat::GREEN . "Majorcraft Core Started" );
		$this->saveDefaultConfig ();
		$this->reloadConfig ();
	}
	
	public function onDisable() {
		$this->getLogger ()->info ( TextFormat::GREEN . "Majorcraft Core Disabled... Did the server stop?" );
	}
	
	public function onLoad() {
		$this->getLogger ()->info ( TextFormat::GREEN . "Majorcraft Core Loaded" );
		if (! file_exists ( $this->getDataFolder () . "chat.yml" )) {
			@mkdir ( $this->getDataFolder () );
			file_put_contents ( $this->getDataFolder () . "chat.yml", $this->getResource ( "chat.yml" ) );
		}
		$this->config = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array("player_join_message","Hey! Welcome to MajorcraftPE");
		$this->badWords = [];
		$words = (new Config ( $this->getDataFolder () . "chat.yml", Config::YAML ))->getAll ();
		foreach ( $words as $word ) {
			$this->badWords [] = $word;
		}
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if (! $sender instanceof Player) {
			$sender->sendMessage ( TextFormat::BLUE . "############################" );
			$sender->sendMessage ( TextFormat::BLUE . "#" . TextFormat::GREEN . " Use this command in-game" . TextFormat::BLUE . " #" );
			$sender->sendMessage ( TextFormat::BLUE . "############################" );
			return false;
		} else {
			switch ($command) {
				case "core" :
					{
                        if (!isset ($args[0])) {
							if (! $sender->hasPermission ( "core.command.info" ))
								return;
							$sender->sendMessage ( TextFormat::GOLD . "-------------" );
							$sender->sendMessage ( TextFormat::GREEN . "Majorcraft Core!" );
							$sender->sendMessage ( TextFormat::GOLD . "-------------" );
							$sender->sendMessage ( TextFormat::BLUE . "This core was developed by the" . TextFormat::RED . " Majorcraft" . TextFormat::BLUE . " team." );
							$sender->sendMessage ( TextFormat::AQUA . "The core was a project inspired by the very great server, LBSG and has taken forever (a very long time)!" );
                            $sender->sendMessage(TextFormat::AQUA . "Core version: " . TextFormat::BLUE . "0.0.1-alpha.2 UNSTABLE");
							return true;
							break;
						}
					}
				case "commands" :
					{
							$sender->sendMessage ( TextFormat::GOLD . "------------------" );
							$sender->sendMessage ( TextFormat::AQUA . "Majorcraft Factions!" );
							$sender->sendMessage ( TextFormat::GOLD . "------------------" );
							$sender->sendMessage ( TextFormat::BLUE . "Factions Help:" . TextFormat::GREEN . " /fhelp" );
							$sender->sendMessage ( TextFormat::BLUE . "Mail Help:" . TextFormat::GREEN . " /mhelp" );
							$sender->sendMessage ( TextFormat::YELLOW . "Vote: " . TextFormat::GREEN . "majorcraft.xyz/vote" );
							$sender->sendMessage ( TextFormat::DARK_AQUA . "-----------------------" );
							return true;
							break;
						}
					}

                case "fhelp" :
					{
						$sender->sendMessage ( TextFormat::GREEN . "-----------" );
						$sender->sendMessage ( TextFormat::GOLD . "Factions Help" );
						$sender->sendMessage ( TextFormat::GREEN . "-----------" );
						$sender->sendMessage ( TextFormat::BLUE . "Main Command:" . TextFormat::AQUA . " /f" );
						$sender->sendMessage ( TextFormat::BLUE . "Create Faction:" . TextFormat::AQUA . " /f create <name>" );
						$sender->sendMessage ( TextFormat::BLUE . "Invite Players:" . TextFormat::AQUA . " /f invite <player>" );
						$sender->sendMessage ( TextFormat::GREEN . "More Commands:" . TextFormat::YELLOW . " /f help" );
						return true;
						break;
					}

                case "mhelp" :
					{
						$sender->sendMessage ( TextFormat::GOLD . TextFormat::BOLD . "Feature coming soon!" );
						return true;
						break;
					}

                case "secretcommand" :
					{
						$sender->sendMessage ( "Shhhh! You found the secret command!" );
					}
				case "nick" :
					{
						if ($sender->hasPermission ( "core.command.nick.use" )) {
                            if (empty ($args[0])) {
								$sender->sendMessage ( TextFormat::RED . "Please enter a valid player name..." );
								return true;
							}
                            if (empty ($args[1])) {
								$sender->sendMessage ( TextFormat::RED . "Please enter a valid nick." );
								return true;
							}
                            $playerName = $args[0];
							$p = $sender->getServer ()->getPlayerExact ( $playerName );
							if ($p == null) {
								$sender->sendMessage ( TextFormat::RED . "player " . TextFormat::AQUA . $playerName . " is not online!" );
								return true;
							}
                            $nick = $args[1];
							$this->plugin->nick_config ( $p->getName () . ".nick", $nick );

                            $this->plugin->formatterPlayerDisplayName ( $p );
							$sender->sendMessage ( TextFormat::GREEN . $p->getName () . " set to " . $args [1] );
							break;
						}
					}
			}
		}
	}

    public function onPlayerChat(PlayerChatEvent $event) {
        $player = $event->getPlayer();
	$event->setMessage($this->filterBadwords($event->getMessage()));
	//$event->setMessage ( $this->filterbadwords ( $m, $this->badWords ) );
    	//if ((!$player->isOp()) && ($event->preg_match($this->filterBadwords($m, $this->badWords)))) {
        	//$player->sendMessage(TextFormat::RED . "Watch your language");
        	//$event->setCancelled();
    	//}
    }

    public function filterBadwords($text, $replaceChar = '*')
    {
        return preg_replace_callback(array_map(function ($w) {
            return '/\b' . preg_quote($w, '/') . '\b/i';
        }, $this->badWords), function ($match) use ($replaceChar) {
            return str_repeat($replaceChar, strlen($match [0]));
        }, $text);
	}
}
	public function onPlayerJoin(PlayerJoinEvent $event){
	
		$player = $event->getPlayer();
		$player->sendMessage( TextFormat::BLUE . $this->api->plugin->readYAML($this->api->plugin->configPath($this). "config.yml");
	}
