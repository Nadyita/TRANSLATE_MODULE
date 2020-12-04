<?php declare(strict_types=1);

namespace Nadybot\User\Modules\TRANSLATE_MODULE;

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

use Nadybot\Core\CommandReply;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateController {

	public string $moduleName;

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
			return "Either the source or the target language is not supported.";
		} catch (\UnexpectedValueException $e) {
			$translation = "An unexpected error occurred while translating.";
		}
		return $translation ?? 'No translation available.';
	}

	/**
	 * Command to translate between arbitrary languages
	 *
	 * @HandlesCommand("translate")
	 * @Matches("/^translate\s+([a-z]{2})(?:\.\.|-)([a-z]{2})\s+(.+)$/i")
	 */
	public function translate2Command(string $message, string $channel, string $sender, CommandReply $sendto, array $args): void {
		$tr = new GoogleTranslate($args[2], $args[1], ['timeout' => 10]);
		$sendto->reply($this->safeTranslate($tr, $args[3]));
	}
	
	/**
	 * Command to translate from given language into English
	 *
	 * @HandlesCommand("translate")
	 * @Matches("/^translate\s+([a-z]{2})\s+(.+)$/i")
	 */
	public function translate1Command(string $message, string $channel, string $sender, CommandReply $sendto, array $args): void {
		$tr = new GoogleTranslate('en', $args[1], ['timeout' => 10]);
		$sendto->reply($this->safeTranslate($tr, $args[2]));
	}

	/**
	 * Command to translate from any language into English
	 *
	 * @HandlesCommand("translate")
	 * @Matches("/^translate\s+(.+)$/i")
	 */
	public function translate0Command(string $message, string $channel, string $sender, CommandReply $sendto, array $args): void {
		$tr = new GoogleTranslate('en', null, ['timeout' => 10]);
		$sendto->reply($this->safeTranslate($tr, $args[1]));
	}
}
