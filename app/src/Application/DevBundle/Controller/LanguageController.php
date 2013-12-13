<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
* DeskPRO
*
* @package DeskPRO
*/

namespace Application\DevBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\DeskPRO\Translate;
use Application\DeskPRO\App;
use Symfony\Component\HttpFoundation\Response;

use Application\DevBundle\Twig\PreservingLexer;
use Application\DevBundle\Language\Language;

use Orb\Util\Strings;
use Orb\Util\Arrays;

class LanguageController extends Controller
{
    public function indexAction()
    {
		return $this->render('DevBundle:Language:index.html.twig', array('bundles' => Language::$BUNDLES, 'bundle_map' => Language::$BUNDLES_MAP, 'packages' => Language::$PACKAGES));
    }

	public function showContextUserAction()
	{
		$vars = array();
		$found_by_id = array();
		$errors = array();
		$prefixes = array();

		Language::GetPhraseFinder($this->container)->getPhrasesFromTwigFiles('UserBundle', $found_by_id, $errors, $prefixes);
		Language::GetPhraseFinder($this->container)->getPhrasesFromTwigFiles('DeskPRO', $found_by_id, $errors, $prefixes);

		$php_files = Language::GetFileFinder()->getPhpFileList('UserBundle');
		$files = Language::GetFileFinder()->getLanguageFileList('user');

		sort($files);
		$view_struct = array();

		foreach($files as $file) {
			$phrases = require($file);
			$key = 'user.'.basename($file, '.php');
			ksort($phrases);

			$view_struct[$key] = array();

			foreach($phrases as $id => $phrase) {
				$parts = explode("\n", $phrase, 2);

				foreach($php_files as $php_file) {
					$data = file_get_contents($php_file);

					$lines = explode("\n", $data);

					foreach($lines as $i => $line) {
						if(strpos($line, $id) !== false) {
							if(!isset($found_by_id[$id])) {
								$found_by_id[$id] = array();
							}

							$found_by_id[$id][] = array('filename' => $php_file, 'line' => $i);
						}
					}
				}

				if(count($parts)-1) $phrase = $parts['0'].' ...';
				$vid = preg_replace('/^user\.[^.]+\./', '', $id);

				if(isset($found_by_id[$id])) {
					foreach($found_by_id[$id] as $k => $found) {
						$found_by_id[$id][$k]['filename'] = preg_replace('/^.*?UserBundle/', '', $found_by_id[$id][$k]['filename']);
						$found_by_id[$id][$k]['filename'] = preg_replace('#^.*?/views/#', '', $found_by_id[$id][$k]['filename']);
						$found_by_id[$id][$k]['filename'] = preg_replace('/.html.twig$/', '', $found_by_id[$id][$k]['filename']);
					}

					$view_struct[$key][$vid] = array('text' => $phrase, 'files' => $found_by_id[$id]);
				}
				else {
					$view_struct[$key][$vid] = array('text' => $phrase, 'files' => array());
				}
			}
		}

		$vars['tree'] = $view_struct;

		return $this->render('DevBundle:Language:show_context_user.html.twig', $vars);
	}

    public function batchReplaceAction()
    {
        set_time_limit(0);
        $changed = array();
        $files = Language::GetFileFinder()->getLanguageFileList();
        $files = array_merge($files, Language::GetFileFinder()->getPhpFileList());
        $files = array_merge($files, Language::GetFileFinder()->getTwigFileList());

        if(isset($_POST['replacements'])) {
            $lines = explode("\n", $_POST['replacements']);
            $replace = array();

            foreach($lines as $i=>$line) {
                $line = trim($line);

                if(empty($line)) {
                    continue;
                }

                $old_new = explode(' ', $line);

                if(count($old_new) == 0) {
                    continue;
                }

                if(count($old_new) != 2) {
                    die('Must have a pair at line '.$i.'!');
                }

                $replace[] = array($old_new[0], $old_new[1]);
            }

            foreach($files as $file) {
                $raw_original = $raw = file_get_contents($file);

                foreach($replace as $pair) {
                    list($old, $new) = $pair;

                    $raw = str_replace($old, $new, $raw);
                }

                if($raw != $raw_original) {
                    $changed[$file] = 1;
                    file_put_contents($file, $raw);
                }
            }
        }

        return $this->render('DevBundle:Language:batch.replace.html.twig', array('changed' => array_keys($changed)));
    }

    public function spellcheckAction()
    {

        $strings = '';

        $files = Language::GetFileFinder()->getLanguageFileList('user');

        foreach ($files AS $file) {

            $phrases = require($file);
            foreach ($phrases AS $id => $text) {
                $strings .= $text . "\n\n";
            }
        }

        $vars = array('strings' => $strings);

        return $this->render('DevBundle:Language:spell.html.twig', $vars);

    }

    public function reformatLanguageFilesAction()
    {
        $files = Language::GetFileFinder()->getLanguageFileList();

        foreach($files as $file) {

            $phrases = require($file);
            $lengths = array();
            ksort($phrases);
            $data = "<?php return array(\n";
            $pairs = array();

            foreach($phrases as $id=>$text) {

				$text = Strings::standardEol($text);
				$text = str_replace("\n", ' ', $text);
				$text = trim($text);

                $id = var_export($id, true);
                $text = var_export($text, true);

                $lengths[] = strlen($id);
                $pairs[$id] = $text;
            }

            $length = max($lengths);

            foreach($pairs as $id=>$text) {
                $id = str_pad($id, $length);
                $data .= "\t{$id} => {$text},\n";
            }

            $data .= ');';

			if ($data != file_get_contents($file)) {
				file_put_contents($file, $data);
			}
        }

        return $this->render('DevBundle:Language:index.html.twig', array('bundles' => Language::$BUNDLES, 'bundle_map' => Language::$BUNDLES_MAP, 'packages' => Language::$PACKAGES, 'message' => 'Reformatted Language Files'));

    }

    public function exportToPOAction($package)
    {
        set_time_limit(0);

        $files = Language::GetFileFinder()->getLanguageFileList($package);
        $strings = array();

        foreach($files as $file) {
            $strings = array_merge(require($file), $strings);
        }

        $fs = fopen(DP_ROOT.'/tmp.csv', 'w');
        fputcsv($fs, array('location', 'source', 'target'));

        /*foreach($strings as $source => $target) {
            fputcsv($fs, array('', $source, str_replace("\n",'\n', $target)));
        }*/

        fclose($fs);
        //echo shell_exec('csv2po '.DP_ROOT.DIRECTORY_SEPARATOR.'tmp.csv '.DP_ROOT.DIRECTORY_SEPARATOR.'tmp.po');

        $fs = fopen(DP_ROOT.'/tmp.po', 'a');

        foreach($strings as $source => $target) {

            fwrite($fs, "\nmsgid \"{$source}\"\n");
            fwrite($fs, "msgstr ");
            $parts = explode("\n", $target);

            foreach($parts as $i=>$part) {
                fwrite($fs, '"'.$part);

                if($i != count($parts) -1) {
                    fwrite($fs, '\n');
                }

                fwrite($fs, "\"\n");
            }
        }

        fclose($fs);

        $response = new Response();
        $response->headers->set('Content-Type','text/po');
        $response->headers->set('Content-Disposition', ' attachment; filename=languages_'.$package.'.po');
        $response->setContent(file_get_contents(DP_ROOT.DIRECTORY_SEPARATOR.'tmp.po'));
        unlink(DP_ROOT.DIRECTORY_SEPARATOR.'tmp.csv');
        unlink(DP_ROOT.DIRECTORY_SEPARATOR.'tmp.po');

        return $response;
    }

    public function exportPOAction($package)
    {

         $folder = DP_ROOT.'/languages/default/'.$package;

         $dh = opendir($folder);

         while(($filename = readdir($dh)) !== false) {

             $filepath = $folder.'/'. $filename;
             if (is_file($filepath)) {

                 $strings = require($filepath);

                 $outfile = DP_ROOT.'/languages/default/' . $package . '/' . 'export' . '/' . str_replace('.php', '', $filename) . '.po';

                 echo "Exporting: {$filepath} <br />";

                 $fs = fopen($outfile, 'w');

                 fwrite($fs, 'msgid ""' . "\n");
                 fwrite($fs, 'msgstr ""' . "\n");
                 fwrite($fs, '"MIME-Version: 1.0\n"' . "\n");
                 fwrite($fs, '"Content-Type: text/plain; charset=UTF-8\n"' . "\n");
                 fwrite($fs, '"Content-Transfer-Encoding: 8bit\n"' . "\n");

                 foreach($strings as $source => $target) {

                     fwrite($fs, "\nmsgid \"{$source}\"\n");
                     fwrite($fs, "msgstr ");

                     $parts = explode("\n", $target);
                     foreach($parts as $i=>$part) {

                         // escape " for PO format
                         fwrite($fs, '"'. str_replace('"', '\\"', $part));

                         if($i != count($parts) -1) {
                             fwrite($fs, '\n');
                         }

                         fwrite($fs, "\"\n");
                     }
                 }

                 fclose($fs);
             }
         }

         closedir($dh);
         return $this->render('DevBundle:Language:index.html.twig', array('bundles' => Language::$BUNDLES, 'bundle_map' => Language::$BUNDLES_MAP, 'packages' => Language::$PACKAGES, 'message' => 'Exported PO files'));
    }

    public function exportAllToPOAction()
    {
        $packages = array('user', 'agent', 'admin');

        foreach($packages as $package) {
            $files = array();
            $folder = DP_ROOT.'/languages/default/'.$package;

            $dh = opendir($folder);

            while(($file = readdir($dh)) !== false) {
                $file = $folder.'/'.$file;

                if(is_file($file)) {
                    $files[] = $file;
                }
            }

            closedir($dh);

            $strings = array();

            foreach($files as $file) {
                $strings = array_merge(require($file), $strings);
            }

            $file = DP_ROOT.'/languages/default/'.ucfirst($package).'.po';
            echo "Exporting: {$file}\n";
            $fs = fopen($file, 'w');

			fwrite($fs, 'msgid ""' . "\n");
			fwrite($fs, 'msgstr ""' . "\n");
			fwrite($fs, '"MIME-Version: 1.0\n"' . "\n");
			fwrite($fs, '"Content-Type: text/plain; charset=UTF-8\n"' . "\n");
			fwrite($fs, '"Content-Transfer-Encoding: 8bit\n"' . "\n");

            foreach($strings as $source => $target) {

                fwrite($fs, "\nmsgid \"{$source}\"\n");
                fwrite($fs, "msgstr ");
                $parts = explode("\n", $target);

                foreach($parts as $i=>$part) {

					// escape " for PO format
                    fwrite($fs, '"'. str_replace('"', '\\"', $part));

                    if($i != count($parts) -1) {
                        fwrite($fs, '\n');
                    }

                    fwrite($fs, "\"\n");
                }
            }

            fclose($fs);
        }

        return $this->render('DevBundle:Language:index.html.twig', array('bundles' => Language::$BUNDLES, 'bundle_map' => Language::$BUNDLES_MAP, 'packages' => Language::$PACKAGES, 'message' => 'Exported PO files'));
    }

    public function findProblemsAction()
    {
        set_time_limit(0);

        if(isset($_POST['content'])) {
            $this->globaliseString($_POST['content'], $_POST['id'],
                Language::GetPhraseFinder($this->container)->getPhrasesFromTwigFiles(null, $by_id, $errors, $prefixes),
                Language::GetPhraseFinder($this->container)->getPhrasesFromPHPFiles(null, $by_id, $errors, $prefixes, $by_file));
        }

        $vars = array(
            'dupes' => array
            (
                'id' => array(),
                'content' => array(),
                'fuzzy_content' => array(),
            ),
            'phrases' => array
            (
                'id' => array(),
                'file' => array(),
            ),
            'prefixes' => array(),
            'missing' => array(),
            'errors' => array(),
        );

        list($by_id, $by_content) = $this->parseLangFiles();

        foreach($by_id as $k=>$v) {
            if(count($v) > 1) {
                $vars['dupes']['id'][] = $k;
            }
        }

        $id_track = array();

        foreach($by_content as $k=>$v) {
            if(count($v) > 1) {
                $packages = array('user' => array(),'admin' => array(), 'agent' => array());

                foreach($v as $dupe) {
                    list($package, ) = explode('.', $dupe['id']);

                    $packages[$package][] = $dupe['id'];
                }

                $packages['admin_agent'] = array_merge($packages['agent'], $packages['admin']);

                if(count($packages['user']) > 1) {
                    $id = 'user.global.'.$this->stringToId($k);
                    $id_exists = isset($by_id[$id]) || isset($id_track[$id]);
                    $id_track[$id] = 1;
                    $vars['dupes']['content'][] = array('data' => $k, 'id' => $id, 'exists' => $id_exists, 'ids' => $packages['user']);
                }

                if(count($packages['admin_agent']) > 1) {
                    if(count($packages['admin_agent']) == count($packages['admin'])) {
                        $id = 'admin.general.'.$this->stringToId($k);
                    }
                    else {
                        $id = 'agent.global.'.$this->stringToId($k);
                    }

                    $id_exists = isset($by_id[$id]) || isset($id_track[$id]);
                    $id_track[$id] = 1;
                    $vars['dupes']['content'][] = array('data' => $k, 'id' => $id, 'exists' => $id_exists, 'ids' => $packages['admin_agent']);
                }
            }
        }

		$vars['by_id'] = $by_id;
		$vars['by_content'] = $by_content;

		$wrong_file = array();

		foreach($by_id as $id => $instances) {
			foreach($instances as $data) {
				$real_file = basename($data['filename'], 'php');
				$real_folder = basename(dirname($data['filename']));
				$real_path = $real_folder . '/' . $real_file;

				if (strpos($id, '.') === false) {
					echo "INVALID: $id<br/>";
					continue;
				}

				list($expect_folder, $rest) = explode('.', $id, 2);

				$parts = explode('.', $rest, 3);

				$expect_file = array_shift($parts);

				if(count($parts) == 2) {
					$expect_file .= '.' . array_shift($parts);
				}

				$expect_path = $expect_folder . '/' . $expect_file;

				if($expect_path != $expect_file) {
					$wrong_file[] = array('id' => $id, 'realpath' => $real_path, 'expectpath' => $expect_path);
				}
			}
		}

		$vars['wrong_file'] = $wrong_file;

        $found_by_id = array();
        $errors = array();
        $prefixes = array();
        $by_file = array();

        Language::GetPhraseFinder($this->container)->getPhrasesFromTwigFiles(null, $found_by_id, $errors, $prefixes);
        Language::GetPhraseFinder($this->container)->getPhrasesFromPHPFiles(null, $found_by_id, $errors, $prefixes, $by_file);
        $missing = $this->getMissing(array_keys($found_by_id));

        $vars['unused'] = array();

        foreach($vars['by_id'] as $id => $value) {
            if(!isset($found_by_id[$id])) {
                $vars['unused'][] = array(
                    'id' => $id,
                    'display' => $this->matchesPrefix($id, $prefixes)
                );
            }
        }

        $vars['missing'] = $missing;
        $vars['errors'] = $errors;
        $vars['prefixes'] = $prefixes;
        $vars['instances']['id'] = $found_by_id;
        $vars['instances']['file'] = $by_file;

        return $this->render('DevBundle:Language:find_problems.html.twig', $vars);
    }

    public function showUsefulAction()
    {
        set_time_limit(0);

        $vars = array(
            'prefixes' => array(),
            'errors' => array(),
			'dupes' => array('fuzzy' => array()),
        );

		list($by_id, $by_content) = $this->parseLangFiles();
		$by_fuzzy = array();

		foreach($by_content as $k=>$v) {
			$k = strtolower($k);
			$k = preg_replace('/\{\{[^}]+]\}\}/', '{{}}', $k);
			$k = preg_replace('/[^a-z{}|]/', '', $k);

			if(!isset($by_fuzzy[$k])) {
				$by_fuzzy[$k] = array();
			}

			$by_fuzzy[$k] = array_merge($by_fuzzy[$k], $v);
		}

		$vars['by_fuzzy'] = $by_fuzzy;

		foreach($by_fuzzy as $k=>$v) {
			if(count($v) > 1) {
				$packages = array('user' => array(),'admin' => array(), 'agent' => array());

				foreach($v as $dupe) {
					list($package, ) = explode('.', $dupe['id']);

					$packages[$package][] = $dupe['id'];
				}

				$packages['admin_agent'] = array_merge($packages['agent'], $packages['admin']);

				if(count($packages['user']) > 1) {
					$id = 'user.global.'.$this->stringToId($k);
					$id_exists = isset($by_id[$id]) || isset($id_track[$id]);
					$id_track[$id] = 1;
					$vars['dupes']['content'][] = array('data' => $k, 'id' => $id, 'exists' => $id_exists, 'ids' => $packages['user']);
				}

				if(count($packages['admin_agent']) > 1) {
					if(count($packages['admin_agent']) == count($packages['admin'])) {
						$id = 'admin.general.'.$this->stringToId($k);
					}
					else {
						$id = 'agent.global.'.$this->stringToId($k);
					}

					$id_exists = isset($by_id[$id]) || isset($id_track[$id]);
					$id_track[$id] = 1;
					$vars['dupes']['fuzzy'][] = array('data' => $k, 'id' => $id, 'exists' => $id_exists, 'ids' => $packages['admin_agent']);
				}
			}
		}

		$vars['by_id'] = $by_id;
        $found_by_id = array();
        $errors = array();
        $prefixes = array();
        $by_file = array();

        Language::GetPhraseFinder($this->container)->getPhrasesFromTwigFiles(null, $found_by_id, $errors, $prefixes);
        Language::GetPhraseFinder($this->container)->getPhrasesFromPHPFiles(null, $found_by_id, $errors, $prefixes, $by_file);

        $vars['errors'] = $errors;
        $vars['prefixes'] = $prefixes;

        return $this->render('DevBundle:Language:show.useful.html.twig', $vars);
    }

    public function showWordCountAction() {
        $directories = array(
            array('user', 'User Interface'),
            array('agent', 'Agent Interface'),
            array('admin', 'Admin Interface')
        );

        $t_wordcount = 0;
        $t_keycount = 0;
        echo '<a href="'.$this->generateUrl('dev_lang_index').'">Back</a><pre>';

        foreach ($directories AS $interface) {

            $keycount = 0;
            $wordcount = 0;

            $dir = DP_ROOT . '/languages/default/' . $interface['0'];

            if ($handle = opendir($dir)) {
                while (false !== ($filename = readdir($handle))) {
                    if ($filename != "." && $filename != "..") {

                        $words = include($dir . '/' . $filename);
                        $keycount += count($words);
                        foreach ($words AS $key => $var) {
                            $wordcount += str_word_count($var);
                        }
                    }
                }

                closedir($handle);

                echo $interface['1'] . " :: $wordcount words in $keycount phrases\n";
                $t_wordcount += $wordcount;
                $t_keycount += $keycount;

            }
        }

        return new Response();
    }

    public function matchesPrefix($id, $prefixes) {
        foreach($prefixes as $prefix) {
            if(preg_match('/^('.preg_quote($prefix['id']).')/', $id)) {
                return preg_replace('/^('.preg_quote($prefix['id']).')/', '<b>\1</b>', $id);
            }
        }

        return $id;
    }

    public function getLanguageData()
    {
        $files = Language::GetFileFinder()->getLanguageFileList();
        $data = array();

        foreach($files as $file) {
            $data = array_merge($data, require($file));
        }

        return $data;
    }

    public function getMissing($ids)
    {
        $language_data = $this->getLanguageData();
        $missing = array();

        foreach($ids as $id) {
            if(!array_key_exists($id, $language_data)) {
                $missing[] = $id;
            }
        }

        return $missing;
    }

    public function stringToId($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/[^a-zA-Z0-9_ ]/', '', $string);
        $string = preg_replace('/ +/', ' ', $string);
        $parts = explode(' ', $string);
        $parts = array_slice($parts, 0, 8);
        $string = implode('_', $parts);
        return $string;
    }

    public function replacePhrasesInFiles($files, $from, $to, $prefix = false)
    {
        foreach($files as $file) {
            $lines = file($file['filename']);
            $data = '';

            foreach($lines as $i=>$line) {
                if($prefix) {
                    if(preg_match('/(phrase\(\s*\')'.preg_quote($from, '/').'([^\']+\'\s*[,)])/', $line)) {
                        $new_line = preg_replace('/(phrase\(\s*\')'.preg_quote($from, '/').'([^\']+\'\s*[,)])/', '\1'.$to.'\2', $line);
                        echo "Replacing line ".htmlspecialchars($line)." <br />with ".htmlspecialchars($new_line)."<br /> in {$file['filename']}<hr />";
                        $data .= $new_line;
                    }
                    else {
                        $data .= $line;
                    }
                }
                else {
                    if(preg_match('/(phrase\(\s*\')'.preg_quote($from, '/').'(\'\s*[,)])/', $line)) {
                        $new_line = preg_replace('/(phrase\(\s*\')'.preg_quote($from, '/').'(\'\s*[,)])/', '\1'.$to.'\2', $line);
                        echo "Replacing line ".htmlspecialchars($line)." <br />with ".htmlspecialchars($new_line)."<br /> in {$file['filename']}<hr />";
                        $data .= $new_line;
                    }
                    else {
                        $data .= $line;
                    }
                }
            }

            file_put_contents($file['filename'], $data);
        }
    }

    public function globaliseString($content, $id, $twig_phrases, $php_phrases)
    {
        list($by_id, $by_content) = $this->parseLangFiles();
        $rootdir = DP_ROOT.'/languages/default';

        $parts = explode('.', $id, 3);

        if(count($parts) == 2) {
            $package = $parts[0];
            $filename = $parts[0];
        }
        else {
            $package = $parts[0];
            $filename = $parts[1];
        }

		$path = $rootdir.'/'.$package.'/'.$filename.'.php';

        if(!file_exists($path)) {
            die('Could not load language file: ' . $path);
        }

        $files = $by_content[$content];

        foreach($files as $i=>$file) {
            list($file_package, ) = explode('.', $file['id'], 2);

            if($package == 'user' && $file_package != 'user') {
                unset($files[$i]);
                continue;
            }

            if($package == 'agent' && $file_package == 'user') {
                unset($files[$i]);
                continue;
            }

            $lines = file($file['filename']);
            $data = '';

            foreach($lines as $i => $line) {
                if($i+1 == $file['line']) {
                    echo "Dropping line $line<br />";
                    $data .= "\n";
                }
                else {
                    $data .= $line;
                }
            }

            file_put_contents($file['filename'], $data);
        }

        $global = require($rootdir.'/'.$package.'/'.$filename.'.php');

        if(!isset($global[$id])) {
            $global[$id] = $content;
            $data = '<?php return '.var_export($global, true).';';
            file_put_contents($rootdir.'/'.$package.'/'.$filename.'.php', $data);
        }

        list($by_id, ) = $twig_phrases;

        foreach($files as $file) {
            if(!isset($by_id[$file['id']]))
                continue;

            $this->replacePhrasesInFiles($by_id[$file['id']], $file['id'], $id);
        }

        list($by_id, ) = $php_phrases;

        foreach($files as $file) {
            if(!isset($by_id[$file['id']]))
                continue;

            $this->replacePhrasesInFiles($by_id[$file['id']], $file['id'], $id);
        }
    }

    public function parseLangFiles($package = '')
    {
        $files = Language::GetFileFinder()->getLanguageFileList($package);
        $by_id = array();
        $by_content = array();

        foreach($files as $file) {
            $rawphp = file_get_contents($file);
            $tokens = token_get_all($rawphp);
            $state = 0;

            foreach($tokens as $token) {
                // This is a very minimal parser and may break if the spec changes for lang file definitions.
                if(!is_array($token) || $token[0] != T_WHITESPACE)
                    switch($state) {
                        case 0:
                            if(is_array($token) && $token[0] == T_CONSTANT_ENCAPSED_STRING) {
                                $state++;
                                $id = eval('return '.$token[1].';');
                            }

                            break;
                        case 1:
                            if(is_array($token) && $token[0] == T_DOUBLE_ARROW) {
                                $state++;
                            }
                            else {
                                die('Unexpected token!');
                            }

                            break;
                        case 2:
                            if(is_array($token) && $token[0] == T_CONSTANT_ENCAPSED_STRING) {
                                $content = eval('return '.$token[1].';');
                                $state = 0;

                                if(!isset($by_id[$id])) {
                                    $by_id[$id] = array();
                                }

                                $by_id[$id][] = array(
                                    'filename' => $file,
                                    'content' => $content,
                                    'line' => $token[2]
                                );

                                if(!isset($by_content[$content])) {
                                    $by_content[$content] = array();
                                }

                                $by_content[$content][] = array(
                                    'filename' => $file,
                                    'id' => $id,
                                    'line' => $token[2]
                                );
                            }
                            else {
                                die('Unexpected token in '.$file.'!');
                            }

                            break;
                    }
            }
        }

        return array($by_id, $by_content);
    }

    public function fixMissing($missing) {
        $rootdir = DP_ROOT.'/languages/default/';
        $missing = array_unique($missing);

        foreach($missing as $id) {
            $parts = explode('.', $id, 3);
            $package = array_shift($parts);

            $dst_file = $rootdir.'/'.$package.'/';

            if(count($parts) == 2) {
                $dst_file .= $parts[0].'.php';
            }
            else {
                $dst_file .= $package.'.php';
            }

            if(file_exists($dst_file)) {
                $target = require($dst_file);
            }
            else {
                $target = array();
            }

            if(!isset($target[$id])) {
                $target[$id] = '['.$id.']';
                file_put_contents($dst_file, '<?php return '.var_export($target, true).';');
            }
        }
    }
}