<?php

namespace Budabot\User\Modules\TRANSLATE_MODULE;

/**
 * @author Nadyita (RK5) <nadyita@hodorraid.org>
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'translate',
 *		accessLevel = 'all',
 *		description = 'Translate a word or sentence from one language into the other',
 *		alias       = 'trans',
 *		help        = 'translate.txt'
 *	)
 */

require_once __DIR__.'/vendor/autoload.php';

use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateController {
	
	public $moduleName;

	/**
	 * @var \Budabot\Core\Text $text
	 * @Inject
	 */
	public $text;
	
	/**
	 * @var \Budabot\Core\DB $db
	 * @Inject
	 */
	public $db;
	
	/**
	 * @var \Budabot\Core\Util $util
	 * @Inject
	 */
	public $util;

	/**
	 * @var \Budabot\Core\LoggerWrapper $logger
	 * @Logger
	 */
	public $logger;

	/**
	 * Safe wrapper around the translate API catching errors
	 *
	 * @param \Stichoza\GoogleTranslate\GoogleTranslate $tr The translation object
	 * @param string $message The message to translate
	 * @return string The translated message
	 */
	protected function safeTranslate(GoogleTranslate $tr, string $message): string {
		try {
			$translation = $tr->translate($message);
		} catch (\ErrorException $e) {
			$translation = $e->getMessage();
		} catch (\UnexpectedValueException $e) {
			$translation = "An unexpected error occurred while translating.";
		}
		return $translation ?? 'No translation available.';
	}

	/**
	 * Command to translate between arbitrary languages
	 *
	 * @param string                     $message The full command received
	 * @param string                     $channel Where did the command come from (tell, guild, priv)
	 * @param string                     $sender  The name of the user issuing the command
	 * @param \Budabot\Core\CommandReply $sendto  Object to use to reply to
	 * @param string[]                   $args    The arguments to the disc-command
	 * @return void
	 *
	 * @HandlesCommand("translate")
	 * @Matches("/^translate\s+([a-z]{2})\.\.([a-z]{2})\s+(.+)$/i")
	 */
	public function translate2Command($message, $channel, $sender, $sendto, $args) {
		$tr = new GoogleTranslate($args[2], $args[1], ['timeout' => 10]);
		$sendto->reply($this->safeTranslate($tr, $args[3]));
	}
	
	/**
	 * Command to translate from given language into English
	 *
	 * @param string                     $message The full command received
	 * @param string                     $channel Where did the command come from (tell, guild, priv)
	 * @param string                     $sender  The name of the user issuing the command
	 * @param \Budabot\Core\CommandReply $sendto  Object to use to reply to
	 * @param string[]                   $args    The arguments to the disc-command
	 * @return void
	 *
	 * @HandlesCommand("translate")
	 * @Matches("/^translate\s+([a-z]{2})\s+(.+)$/i")
	 */
	public function translate1Command($message, $channel, $sender, $sendto, $args) {
		$tr = new GoogleTranslate('en', $args[1], ['timeout' => 10]);
		$sendto->reply($this->safeTranslate($tr, $args[2]));
	}
}
