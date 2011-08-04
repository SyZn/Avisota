<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2010,2011 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 * @license    LGPL
 * @filesource
 */


/**
 * Class AvisotaOutbox
 *
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 */
class AvisotaOutbox extends BackendModule
{
	protected $strTemplate = 'be_avisota_outbox';

	public function __construct()
	{
		parent::__construct();
		$this->import('DomainLink');
		$this->import('BackendUser', 'User');
		$this->import('AvisotaBase', 'Base');
		$this->loadLanguageFile('tl_avisota_newsletter');
	}

	/**
	 * (non-PHPdoc)
	 * @see BackendModule::generate()
	 */
	public function generate()
	{
		if ($this->Input->get('act') == 'details')
		{
			$this->strTemplate = 'be_avisota_outbox_details';
		}
		if ($this->Input->get('act') == 'send')
		{
			$this->strTemplate = 'send';
		}

		return parent::generate();
	}


	/**
	 * (non-PHPdoc)
	 * @see BackendModule::compile()
	 */
	protected function compile()
	{
		if (!$this->User->isAdmin && !$this->User->hasAccess('send', 'avisota_newsletter_permissions'))
		{
			$this->log('Not enough permissions to send avisota newsletter', 'Avisota outbox', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$this->loadLanguageFile('tl_avisota_newsletter_outbox');
		$this->loadLanguageFile('tl_avisota_newsletter');

		/*
				if (!$this->Base->allowBackendSending())
				{
					$this->redirect($referer);
				}

				// get the newsletter
				$objNewsletter = $this->Database->prepare("
						SELECT
							*
						FROM
							tl_avisota_newsletter
						WHERE
							id=?")
					->execute($intId);
				if (!$objNewsletter->next())
				{
					$this->redirect($referer);
				}

				// get the category
				$objCategory = $this->Database->prepare("
						SELECT
							*
						FROM
							tl_avisota_newsletter_category
						WHERE
							id=?")
					->execute($objNewsletter->pid);
				if (!$objCategory->next())
				{
					$this->redirect($referer);
				}

				$this->Static->setCategory($objCategory);
				$this->Static->setNewsletter($objNewsletter);

				// get total email count
				$objTotal = $this->Database->prepare("
						SELECT
							COUNT(*) as total
						FROM
							tl_avisota_newsletter_outbox
						WHERE
								pid=?
							AND token=?
							AND send=0")
					->execute($intId, $strToken);

				// Return if there are no recipients
				if ($objTotal->total < 1)
				{
					$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['confirm'];

					$this->redirect($referer);
				}

				// Set timeout and count
				$intTimeout = $GLOBALS['TL_CONFIG']['avisota_max_send_timeout'];
				$intCount = $GLOBALS['TL_CONFIG']['avisota_max_send_count'];

				// Overwrite the SMTP configuration
				if ($objCategory->useSMTP)
				{
					$GLOBALS['TL_CONFIG']['useSMTP'] = true;

					$GLOBALS['TL_CONFIG']['smtpHost'] = $objCategory->smtpHost;
					$GLOBALS['TL_CONFIG']['smtpUser'] = $objCategory->smtpUser;
					$GLOBALS['TL_CONFIG']['smtpPass'] = $objCategory->smtpPass;
					$GLOBALS['TL_CONFIG']['smtpEnc']  = $objCategory->smtpEnc;
					$GLOBALS['TL_CONFIG']['smtpPort'] = $objCategory->smtpPort;
				}

				// Add default sender address
				if (!strlen($objCategory->sender))
				{
					list($objCategory->senderName, $objCategory->sender) = $this->splitFriendlyName($GLOBALS['TL_CONFIG']['adminEmail']);
				}

				$arrAttachments = array();

				// Add attachments
				if ($objNewsletter->addFile)
				{
					$files = deserialize($objNewsletter->files);

					if (is_array($files) && count($files) > 0)
					{
						foreach ($files as $file)
						{
							if (is_file(TL_ROOT . '/' . $file))
							{
								$arrAttachments[] = $file;
							}
						}
					}
				}

				// force all URLs absolute
				$GLOBALS['TL_CONFIG']['forceAbsoluteDomainLink'] = true;

				// create the contents
				$plain = array
				(
					'anonymous' => $this->Content->prepareBeforeSending($this->Content->generatePlain($objNewsletter, $objCategory, 'anonymous')),
					'private' => $this->Content->prepareBeforeSending($this->Content->generatePlain($objNewsletter, $objCategory, 'private'))
				);
				$html = array
				(
					'anonymous' => $this->Content->prepareBeforeSending($this->Content->generateHtml($objNewsletter, $objCategory, 'anonymous')),
					'private' => $this->Content->prepareBeforeSending($this->Content->generateHtml($objNewsletter, $objCategory, 'private'))
				);

				// load tl_avisota_recipient and tl_member data container
				$this->loadDataContainer('tl_avisota_recipient');
				$this->loadDataContainer('tl_member');
				// build a special field deciding select
				$strSelect = '';
				// fields that are allready added (or should not be added)
				$arrFields = array('id', 'tstamp', 'lists');
				// fields from tl_avisota_recipient
				$arrRecipientFields = $this->Database->getFieldNames('tl_avisota_recipient');
				// fields from tl_member
				$arrMemberFields = $this->Database->getFieldNames('tl_member');

				// add all tl_avisota_recipient fields, with fallback on tl_member fields
				foreach ($GLOBALS['TL_DCA']['tl_avisota_recipient']['fields'] as $strField => $arrData)
				{
					if (!in_array($strField, $arrFields) && in_array($strField, $arrRecipientFields) && $arrData['inputType'] != 'password')
					{
						if (in_array($strField, $arrMemberFields) && isset($GLOBALS['TL_DCA']['tl_member']['fields'][$strField]))
						{
							$strSelect .= sprintf('IFNULL(r.%1$s, m.%1$s) as %1$s, ', $strField);
						}
						else
						{
							$strSelect .= sprintf('r.%1$s as %1$s, ', $strField);
						}
						$arrFields[] = $strField;
					}
				}
				// add remeaning tl_member fields
				foreach ($GLOBALS['TL_DCA']['tl_member']['fields'] as $strField => $arrData)
				{
					if (!in_array($strField, $arrFields) && in_array($strField, $arrMemberFields) && $arrData['inputType'] != 'password')
					{
						$strSelect .= sprintf('m.%1$s as %1$s, ', $strField);
						$arrFields[] = $strField;
					}
				}

				// Get recipients
				$objRecipients = $this->Database->prepare("
					SELECT
						t.*,
						t.outbox_email as email
					FROM (
						SELECT
							$strSelect
							m.id as member_id,
							r.id as recipient_id,
							o.email as outbox_email,
							o.id as outbox,
							o.source as outbox_source,
							SUBSTRING(o.email, LOCATE('@', o.email)) as domain
						FROM
							tl_avisota_newsletter_outbox o
						LEFT JOIN
							tl_member m
						ON
								o.email=m.email
							AND m.disable=''
						LEFT JOIN
							tl_avisota_recipient r
						ON
								o.email=r.email
							AND r.pid = SUBSTRING(o.source, 6)
						WHERE
								o.pid=?
							AND o.token=?
							AND o.send=0) t
					GROUP BY
						domain")
					->limit($intCount)
					->execute($intId, $strToken);

				echo '<div style="font-family:Verdana, sans-serif; font-size:11px; line-height:16px; margin-bottom:12px;">';

				// Send newsletter
				if ($objRecipients->numRows > 0)
				{
					if (!$objNewsletter->sendOn)
					{
						$this->Database->prepare("
								UPDATE
									tl_avisota_newsletter
								SET
									sendOn=?
								WHERE
									id=?")
							->execute(time(), $objNewsletter->id);
					}

					$n = 0;
					$s = time();

					$intEndExecutionTime = $_SERVER['REQUEST_TIME'] + $GLOBALS['TL_CONFIG']['avisota_max_send_time'];

					while ($intEndExecutionTime > time() && $objRecipients->next())
					{
						// private recipient (member/recipient id exists)
						if ($objRecipients->id)
						{
							$arrRecipient = $objRecipients->row();
							$personalized = 'private';
						}

						// anonymous recipient
						else
						{
							$arrRecipient = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['anonymous'];
							$arrRecipient['email'] = $objRecipients->email;
							$personalized = 'anonymous';
						}

						// Send
						$objEmail = $this->generateEmailObject($objNewsletter, $objCategory, $arrAttachments);
						if (!$this->sendNewsletter(
								$objEmail,
								$objNewsletter,
								$objCategory,
								$this->prepareTrackingPlain($objNewsletter, $objCategory, $objRecipients, $plain[$personalized]),
								$this->prepareTrackingHtml($objNewsletter, $objCategory, $objRecipients, $html[$personalized]),
								$arrRecipient,
								$personalized))
						{
							$_SESSION['TL_ERROR'][] = sprintf($GLOBALS['TL_LANG']['tl_avisota_newsletter_outbox']['rejected'], $objRecipients->email);

							$this->Database->prepare("
									UPDATE
										tl_avisota_newsletter_outbox
									SET
										failed='1'
									WHERE
										id=?")
								->execute($objRecipients->outbox);

							$this->Database->prepare("
									UPDATE
										tl_avisota_recipient
									SET
										confirmed=''
									WHERE
										email=?")
								->execute($objRecipients->email);

							$this->log('Recipient address "' . $objRecipients->email . '" was rejected and has been deactivated', 'Avisota outbox()', TL_ERROR);
						}

						$this->Database->prepare("
								UPDATE
									tl_avisota_newsletter_outbox
								SET
									send=?
								WHERE
									id=?")
							->execute(time(), $objRecipients->outbox);

						echo 'Sending newsletter to <strong>' . $objRecipients->email . '</strong><br />';
						$n ++;

						ob_flush();
					}

					echo '<div style="margin-top:12px;">Sending ' . $n . ' newletters in ' . (time() - $s) . ' seconds!</div>';
				}

				echo '<div style="margin-top:12px;">';

				// Redirect back home
				if ($objRecipients->numRows == 0)
				{
					$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['confirm'];

					echo '<script type="text/javascript">setTimeout(\'window.location="' . $this->Environment->base . $referer . '"\', 1000);</script>';
					echo '<a href="' . $this->Environment->base . $referer . '">Please click here to proceed if you are not using JavaScript</a>';
				}

				// Redirect to the next cycle
				else
				{
					echo '<script type="text/javascript">setTimeout(\'window.location="' . $this->Environment->base . $this->Environment->request . '"\', ' . ($intTimeout * 1000) . ');</script>';
					echo '<a href="' . $this->Environment->base . $this->Environment->request . '">Please click here to proceed if you are not using JavaScript</a>';
				}

				echo '</div></div>';
				exit;
		*/

		if ($this->Input->get('act') == 'details')
		{
			$this->details();
			return;
		}

		if ($this->Input->get('act') == 'remove')
		{
			$this->remove();
			return;
		}

		if ($this->Input->get('act') == 'send')
		{
			$this->send();
			return;
		}

		$this->outboxes();
	}

	protected function details()
	{
		// get the outbox
		$objOutbox = $this->Database
			->prepare("SELECT
					*,
					(SELECT COUNT(id) FROM tl_avisota_newsletter_outbox_recipient r WHERE o.id=r.pid) as recipients
				FROM
					tl_avisota_newsletter_outbox o
				WHERE
					id=?")
			->execute($this->Input->get('id'));

		if (!$objOutbox->next())
		{
			$this->redirect('contao/main.php?do=avisota_outbox');
		}

		$objNewsletter = $this->Database
			->prepare("SELECT * FROM tl_avisota_newsletter WHERE id=?")
			->execute($objOutbox->pid);

		if (!$objNewsletter->next())
		{
			$this->redirect('contao/main.php?do=avisota_outbox');
		}

		$this->Template->outbox = $objOutbox->row();
		$this->Template->newsletter = $objNewsletter->row();

		$arrSession = $this->Session->get('AVISOTA_OUTBOX');

		if (!$arrSession['offset'] || $arrSession['offset']>$objOutbox->recipients)
		{
			$arrSession['offset'] = 0;
		}
		if (!$arrSession['limit'])
		{
			$arrSession['limit'] = 30;
		}
		if ($this->Input->post('FORM_SUBMIT') == 'tl_filters')
		{
			if ($this->Input->post('tl_filter') == 'all')
			{
				$arrSession['offset'] = 0;
				$arrSession['limit'] = 500;
			}
			else if (preg_match('#^(\d+),(\d+)$#', $this->Input->post('tl_filter'), $m))
			{
				$arrSession['offset'] = intval($m[1]);
				$arrSession['limit'] = intval($m[2]);
			}
			else
			{
				$arrSession['offset'] = 0;
				$arrSession['limit'] = 30;
			}
			$this->Session->set('AVISOTA_OUTBOX', $arrSession);
			$this->reload();
		}

		$this->Session->set('AVISOTA_OUTBOX', $arrSession);
		$this->Template->offset = $arrSession['offset'];
		$this->Template->limit = $arrSession['limit'];

		$arrRecipients = array();
		$objRecipients = $this->Database
			->prepare("SELECT * FROM tl_avisota_newsletter_outbox_recipient WHERE pid=? ORDER BY email")
			->limit($arrSession['limit'], $arrSession['offset'])
			->execute($objOutbox->id);
		while ($objRecipients->next())
		{
			$arrSource = $this->getSource($objRecipients);
			$arrRecipient = $objRecipients->row();
			$arrRecipient['source'] = $arrSource;
			$arrRecipients[] = $arrRecipient;
		}
		$this->Template->recipients = $arrRecipients;
	}

	protected function remove()
	{
		$this->Database
			->prepare("DELETE FROM tl_avisota_newsletter_outbox WHERE id=?")
			->execute($this->Input->get('id'));
		$this->Database
			->prepare("DELETE FROM tl_avisota_newsletter_outbox_recipient WHERE pid=?")
			->execute($this->Input->get('id'));

		$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['tl_avisota_newsletter_outbox']['removed'];

		$this->redirect('contao/main.php?do=avisota_outbox');
	}

	protected function outboxes()
	{
		// allow backend sending
		$this->Template->beSend = $this->Base->allowBackendSending();

		$arrOutbox = array
		(
			'open' => array(),
			'incomplete' => array(),
			'complete' => array()
		);
		$objOutbox = $this->Database->execute("
				SELECT
					o.id,
					n.subject as newsletter,
					o.tstamp,
					(SELECT COUNT(id) FROM tl_avisota_newsletter_outbox_recipient r WHERE r.pid=o.id) as recipients,
					(SELECT COUNT(id) FROM tl_avisota_newsletter_outbox_recipient r WHERE r.pid=o.id AND r.send=0) as outstanding,
					(SELECT COUNT(id) FROM tl_avisota_newsletter_outbox_recipient r WHERE r.pid=o.id AND r.failed='1') as failed
				FROM
					tl_avisota_newsletter_outbox o
				INNER JOIN
					tl_avisota_newsletter n
				ON
					n.id=o.pid
				ORDER BY
					o.tstamp DESC,
					n.subject ASC");
		while ($objOutbox->next())
		{

			// show source-list-names
			$objSource = $this->Database
				->prepare('SELECT source, sourceID, COUNT(id) as recipients FROM tl_avisota_newsletter_outbox_recipient WHERE pid=? GROUP BY source')
				->execute($objOutbox->id);

			$arrSources = array();
			while($objSource->next())
			{
				$arrSource = $this->getSource($objSource);
				if ($arrSource)
				{
					$arrSources[] = array_merge($arrSource, $objSource->row());
				}
			}
			$objOutbox->sources = $arrSources;

			if ($objOutbox->outstanding == $objOutbox->recipients)
			{
				$arrOutbox['open'][] = $objOutbox->row();
			}
			elseif ($objOutbox->outstanding > 0)
			{
				$arrOutbox['incomplete'][] = $objOutbox->row();
			}
			else
			{
				$arrOutbox['complete'][] = $objOutbox->row();
			}
			if ($objOutbox->failed > 0)
			{
				$this->Template->display_failed = true;
			}
		}
		if (count($arrOutbox['open']) || count($arrOutbox[incomplete]) || count($arrOutbox['complete']))
		{
			$this->Template->outbox = $arrOutbox;
		}
		else
		{
			$this->Template->outbox = false;
		}

		return $this->Template->parse();
	}


	protected function send()
	{
		// TODO
	}


	/**
	 * Get a source description from outbox recipient.
	 */
	protected function getSource($objRecipient)
	{
		switch ($objRecipient->source)
		{
		case 'list':
			$objList = $this->Database
				->prepare("SELECT * FROM tl_avisota_recipient_list WHERE id=?")
				->execute($objRecipient->sourceID);
			if ($objList->next())
			{
				$arrSource = $objList->row();
				$arrSource['title'] = sprintf('%s: %s', $GLOBALS['TL_LANG']['tl_avisota_newsletter_outbox']['recipient_list'], $objList->title);
				$arrSource['linkedTitle'] = sprintf('%s: <a href="contao/main.php?do=avisota_recipients&table=tl_avisota_recipient&id=%d">%s</a>', $GLOBALS['TL_LANG']['tl_avisota_newsletter_outbox']['recipient_list'], $objList->id, $objList->title);
				return $arrSource;
			}

		case 'mgroup':
			$objMgroup = $this->Database
				->prepare("SELECT * FROM tl_member_group WHERE id=?")
				->execute($objRecipient->sourceID);
			if ($objMgroup->next())
			{
				$arrSource = $objMgroup->row();
				$arrSource['title'] = sprintf('%s: %s', $GLOBALS['TL_LANG']['tl_avisota_newsletter_outbox']['mgroup'], $objMgroup->name);
				$arrSource['linkedTitle'] = sprintf('%s: <a href="contao/main.php?do=member">%s</a>', $GLOBALS['TL_LANG']['tl_avisota_newsletter_outbox']['mgroup'], $objMgroup->name);
				return $arrSource;
			}
		}
		return false;
	}

	/**
	 * Prepare the html content for tracking.
	 */
	protected function prepareTrackingHtml($objNewsletter, $objCategory, $objRecipient, $strHtml)
	{
		$objPrepareTrackingHelper = new PrepareTrackingHelper($objNewsletter, $objCategory, $objRecipient);
		$strHtml = preg_replace_callback('#href=["\']((http|ftp)s?:\/\/.+)["\']#U', array(&$objPrepareTrackingHelper, 'replaceHtml'), $strHtml);

		$objRead = $this->Database
			->prepare("SELECT * FROM tl_avisota_newsletter_read WHERE pid=? AND recipient=?")
			->execute($objNewsletter->id, $objRecipient->outbox_email);
		if ($objRead->next())
		{
			$intRead = $objRead->id;
		}
		else
		{
			$objRead = $this->Database
				->prepare("INSERT INTO tl_avisota_newsletter_read (pid,tstamp,recipient) VALUES (?, ?, ?)")
				->execute($objNewsletter->id, time(), $objRecipient->outbox_email);
			$intRead = $objRead->insertId;
		}

		if ($objCategory->viewOnlinePage)
		{
			$objPage = $this->getPageDetails($objCategory->viewOnlinePage);
		}
		else
		{
			$objPage = null;
		}

		$strHtml = str_replace('</body>', '<img src="' . $this->DomainLink->absolutizeUrl('nltrack.php?read=' . $intRead, $objPage) . '" alt="" width="1" height="1" />', $strHtml);
		return $strHtml;
	}


	/**
	 * Prepare the plain content for tracking.
	 */
	protected function prepareTrackingPlain($objNewsletter, $objCategory, $objRecipient, $strPlain)
	{
		$objPrepareTrackingHelper = new PrepareTrackingHelper($objNewsletter, $objCategory, $objRecipient);
		return preg_replace_callback('#<((http|ftp)s?:\/\/.+)>#U', array(&$objPrepareTrackingHelper, 'replacePlain'), $strPlain);
	}
}


/**
 * Helper class.
 */
class PrepareTrackingHelper extends Controller
{
	protected $objNewsletter;

	protected $objCategory;

	protected $objRecipient;

	public function __construct($objNewsletter, $objCategory, $objRecipient)
	{
		parent::__construct();
		$this->import('Database');
		$this->import('DomainLink');
		$this->objNewsletter = $objNewsletter;
		$this->objCategory = $objCategory;
		$this->objRecipient = $objRecipient;
	}

	public function replaceHtml($m)
	{
		$strUrl = $this->replace($m[1]);

		if ($strUrl)
		{
			return 'href="' . specialchars($strUrl) . '"';
		}
		return $m[0];
	}

	public function replacePlain($m)
	{
		$strUrl = $this->replace($m[1]);

		if ($strUrl)
		{
			return '<' . specialchars($strUrl) . '>';
		}
		return $m[0];
	}

	public function replace($strUrl)
	{
		// do not track ...
		if (// images
		preg_match('#\.(jpe?g|png|gif)#i', $strUrl)
		// unsubscribe url
		|| preg_match('#unsubscribetoken#i', $strUrl))
		{
			return false;
		}

		$objLink = $this->Database
		->prepare("SELECT * FROM tl_avisota_newsletter_link_hit WHERE pid=? AND url=? AND recipient=?")
		->execute($this->objNewsletter->id, $strUrl, $this->objRecipient->outbox_email);
		if ($objLink->next())
		{
			$intLink = $objLink->id;
		}
		else
		{
			$objLink = $this->Database
			->prepare("INSERT INTO tl_avisota_newsletter_link_hit (pid,tstamp,url,recipient) VALUES (?, ?, ?, ?)")
			->execute($this->objNewsletter->id, time(), $strUrl, $this->objRecipient->outbox_email);
			$intLink = $objLink->insertId;
		}

		if ($this->objCategory->viewOnlinePage)
		{
			$objPage = $this->getPageDetails($this->objCategory->viewOnlinePage);
		}
		else
		{
			$objPage = null;
		}

		return $this->DomainLink->absolutizeUrl('nltrack.php?link=' . $intLink, $objPage);
	}
}