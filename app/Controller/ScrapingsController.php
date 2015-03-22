<?php

/**
 * Class ScrapingsController
 */
class ScrapingsController extends AppController
{
    public $uses = ['Systemtype', 'System', 'Chemical', 'ChemicalsSystem', 'Citation', 'Author', 'AuthorsCitation', 'Variable', 'Table'];

    /**
     * Ingest the systems
     */
    function getsystems()
    {
        $types = $this->Systemtype->find('list', ['fields' => ['Systemtype.id', 'Systemtype.sysID'], 'order' => ['Systemtype.sysID'], 'limit' => 600, 'offset' => 2000]);
        $url = Configure::read('url.system.list');
        foreach ($types as $id => $systypeID) {
            $types[$id] = ['url' => str_replace("*systypeID*", $systypeID, $url)];
            $page = file_get_contents($types[$id]['url']);
            preg_match_all("/goBack=Y&amp;sysID=(.{4,6})\">/", $page, $sysIDs);
            $types[$id]['sysIDs'] = $sysIDs[1];
            foreach ($sysIDs[1] as $sysID) {
                // Add entries to the systems table
                $this->System->create();
                $data = ['System' => ['sysID' => $sysID, 'systemtype_id' => $id, 'updated' => date(DATE_ATOM)]];
                $this->System->save($data);
            }
        }
        $this->set('data', $types);
    }

    /**
     * Scrap the details of a system into the database
     */
    function getdetails()
    {
        $systems = $this->System->find('list', ['fields' => ['System.id', 'System.sysID'], 'order' => ['System.sysID'], 'conditions' => ['title' => ''], 'limit' => 5]);

        $feedback = [];
        foreach ($systems as $id => $sysID) {
            $system = ['System' => ['id' => $id]];
            $url = str_replace("*sysID*", $sysID, Configure::read('url.system.detail'));

            $page = file_get_contents($url);
            list(, $page) = explode("NIST Standard Reference Database 106", $page);
            $page = str_replace([" style=\"color:#663300;font-size:12pt;\"", "&nbsp;"], "", $page);
            $page = str_replace("<font color=\"#663300\" size=\"3\">", "", $page);

            // For system name match MainContentPlaceHolder_lbl_SysName
            $match = '/MainContentPlaceHolder_lbl_SysName">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            (isset($data[1][0])) ? $system['System']['title'] = $this->clean($data[1][0]) : $system['System']['title'] = "";

            // For component data match MainContentPlaceHolder_lbl_compValue
            $match = '/MainContentPlaceHolder_lbl_compValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            if (isset($data[1][0])) {
                $comps = str_ireplace("<br>", "\t", $data[1][0]);
                $comps = strip_tags($comps);
                $comps = str_replace(["NIST Chemistry WebBook for detail", "[", "]"], "", $comps);
                $comps = preg_replace('/\(\d{1,2}\) /', "", $comps);
                $comps = str_replace("; ", "**", $comps);
                $system['Chemical'] = array_filter(explode("\t", trim($comps)));
            } else {
                $system['Chemical'] = "";
            }

            // For original measurement data (citation) match MainContentPlaceHolder_lbl_MeasValue
            $match = '/MainContentPlaceHolder_lbl_MeasValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            (isset($data[1][0])) ? $system['Citation'] = $this->clean($data[1][0]) : $system['citation'] = "";

            // For variable data match MainContentPlaceHolder_lbl_VariValue
            $match = '/MainContentPlaceHolder_lbl_VariValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            if (isset($data[1][0])) {
                $vars = str_ireplace("<br>", "\t", $data[1][0]);
                $vars = strip_tags($vars);
                $system['Variable'] = array_filter(explode("\t", $vars));
            } else {
                $system['Variable'] = "";
            }

            // For preparer data match MainContentPlaceHolder_lbl_prepValue
            $match = '/MainContentPlaceHolder_lbl_prepValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            (isset($data[1][0])) ? $system['System']['preparer'] = $this->clean($data[1][0]) : $system['System']['preparer'] = "";

            // For experimental remarks data match MainContentPlaceHolder_lbl_ExpRValue
            $match = '/MainContentPlaceHolder_lbl_ExpRValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            (isset($data[1][0])) ? $system['System']['remarks'] = $this->clean($data[1][0]) : $system['System']['remarks'] = "";

            // For experimental data  match MainContentPlaceHolder_lbl_ExpDValue
            $match = '/MainContentPlaceHolder_lbl_ExpDValue">(.*)<span id=\"MainContentPlaceHolder_lbl_EvalEDate/is';
            preg_match_all($match, $page, $data);
            (isset($data[1][0])) ? $system['System']['data'] = str_replace(["\r", "\n\n\n\n", "\n\n\n", "\n\n", "\n"], ["", "<br />\n", "<br />\n", "<br />\n", "<br />\n"], strip_tags($data[1][0])) : $system['System']['data'] = ""; // needed becuase of \n characters

            // For experimental data table match MainContentPlaceHolder_lbl_Table
            $match = '/MainContentPlaceHolder_lbl_Table">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            $tables = explode("</table>", $data[1][0]);
            foreach ($tables as $tid => $table) {
                $tables[$tid] = $this->table2json($table);
            }
            if ($tables[count($tables) - 1] == "[]") {
                unset($tables[count($tables) - 1]);
            }
            (isset($data[1][0])) ? $system['Table'] = $tables : $system['Table'] = "";

            // For experimental data note data match MainContentPlaceHolder_lbl_NoteValue
            $match = '/MainContentPlaceHolder_lbl_NoteValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            if (isset($data[1][0])) {
                $notes = str_ireplace("<br>", "\t", $data[1][0]);
                $notes = strip_tags($notes);
                if ($notes != "") {
                    $system['System']['datanotes'] = json_encode(array_filter(explode("\t", $notes)));
                }
            } else {
                $system['System']['datanotes'] = "";
            }

            // For method data match MainContentPlaceHolder_lbl_MethValue
            $match = '/MainContentPlaceHolder_lbl_MethValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            (isset($data[1][0])) ? $system['System']['method'] = $this->clean($data[1][0]) : $system['System']['method'] = "";

            // For source data match MainContentPlaceHolder_lbl_SrcValue
            $match = '/MainContentPlaceHolder_lbl_SrcValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            if (isset($data[1][0])) {
                $source = str_ireplace("<br>", "\t", $data[1][0]);
                $source = strip_tags($source);
                $source = preg_replace('/\(\d{1,2}\) /', "", $source);
                if ($source !== "") {
                    $system['System']['source'] = json_encode(array_filter(explode("\t", $source)));
                } else {
                    $system['System']['source'] = "";
                }
            } else {
                $system['System']['source'] = "";
            }

            // For error data match MainContentPlaceHolder_lbl_ErrValue
            $match = '/MainContentPlaceHolder_lbl_ErrValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            (isset($data[1][0])) ? $system['System']['errors'] = $this->clean($data[1][0]) : $system['System']['errors'] = "";

            // For references data match MainContentPlaceHolder_lbl_RefValue
            $match = '/MainContentPlaceHolder_lbl_RefValue">(.*)<\/span>/i';
            preg_match_all($match, $page, $data);
            if (isset($data[1][0])) {
                $refs = str_replace("<br>", "\t", $data[1][0]);
                $refs = strip_tags($refs);
                if (str_replace("\t", "", $refs) == "") {
                    $system['System']['refs'] = "";
                } else {
                    $system['System']['refs'] = json_encode(array_filter(explode("\t", $refs)));
                }
            } else {
                $system['System']['refs'] = "";
            }

            // Save data

            // Citation (and Authors) (Can't handle Ph.D. dissertations as refs)
            if (isset($system['Citation']) && $system['Citation'] != "") {
                $citation = ['system_id' => $id];
                // Deconstruction citation text
                $system['Citation'] = preg_replace("/(\d),(\d)/", "$1-$2", $system['Citation']);
                $part = explode(".,", $system['Citation'], 2); // Authors => $part[0], rest => $part[1]
                if (stristr($part[0], ";")):    $authors = explode("; ", $part[0] . "."); // Add back the period that was lost on explode
                else:                        $authors = [$part[0] . "."]; // For only one author
                endif;
                foreach ($authors as $key => $author) {
                    $temp = explode(", ", $author);
                    $authors[$key] = ['lastname' => $temp[0], 'firstname' => $temp[1]];
                }
                $rest = explode(", ", $part[1]); // Journal/Year => $citation[0], Volume/Issue $citation[1], pages => $citation[2]
                if (count($rest) == 2) {
                    $rest = [$rest[0], "", $rest[1]];
                } // Assume vol/issue is missing if only two parts
                // Journal/Year
                if ($rest[0] != "") {
                    $auyr = explode(" ", strrev($rest[0]), 2);
                    $citation['journal'] = trim(strrev($auyr[1]));
                    $citation['year'] = strrev($auyr[0]);
                }
                // Volume/Issue
                if ($rest[1] != "") {
                    if (stristr($rest[1], "(")) {
                        $vois = preg_replace("/(\d*)\((.*)\)/", "$1,$2", $rest[1]);
                        list($citation['volume'], $citation['issue']) = explode(",", $vois);
                    } else {
                        $citation['volume'] = $rest[1];
                    }
                }
                // Pages
                if ($rest[2] != "") {
                    if (stristr($rest[2], "-")) {
                        list($citation['firstpage'], $citation['lastpage']) = explode("-", str_replace(".", "", $rest[2]), 2);
                        if (strlen($citation['firstpage']) > strlen($citation['lastpage'])) {
                            $len1 = strlen($citation['firstpage']);
                            $len2 = strlen($citation['lastpage']);
                            $citation['lastpage'] = substr($citation['firstpage'], 0, $len1 - $len2) . $citation['lastpage'];
                        }
                    } else {
                        $citation['firstpage'] = str_replace(".", "", $rest[2]);
                    }
                }

                // Do DOI lookup via Crossref (get article title and full names of authors)
                $HttpSocket = new HttpSocket();
                $get = ['pid' => 'schalk@unf.edu', 'noredirect' => 'true'];
                $get['aulast'] = str_replace([",", "."], "", $authors[0]);
                if (isset($citation['journal'])) {
                    $get['title'] = $citation['journal'];
                }
                if (isset($citation['volume'])) {
                    $get['volume'] = $citation['volume'];
                }
                if (isset($citation['issue'])) {
                    $get['issue'] = $citation['issue'];
                }
                if (isset($citation['firstpage'])) {
                    $get['spage'] = $citation['firstpage'];
                }
                if (isset($citation['year'])) {
                    $get['date'] = $citation['year'];
                }
                $response = $HttpSocket->get("http://www.crossref.org/openurl", $get);
                $meta = $this->xmlToArray($response['body']);
                $meta = $meta['crossref_result']['query_result']['body']['query'];
                $feedback[] = $meta;
                if (isset($meta['doi']['@'])) {
                    $citation['doi'] = $meta['doi']['@'];
                    $citation["url"] = "http://doi.dx.org/" . $meta['doi']['@'];
                }
                if (isset($meta['journal_title']['@'])) {
                    $citation['journal'] = $meta['journal_title']['@'];
                }
                if (isset($meta['article_title'])) {
                    $citation['title'] = $meta['article_title'];
                }
                if (isset($meta['contributors']['contributor'])) {
                    $authors = []; // Deletes out authors obtained from citation
                    (!isset($meta['contributors']['contributor'][0])) ? $aus = [$meta['contributors']['contributor']] : $aus = $meta['contributors']['contributor'];
                    foreach ($aus as $au) {
                        $authors[] = ['firstname' => $au['given_name'], 'lastname' => $au['surname']];
                    }
                }

                // Add citation if it does not already exist
                $existing = false;
                if (isset($citation['doi'])) {
                    $result = $this->Citation->find('first', ['fields' => ['id'], 'conditions' => ['doi' => $citation['doi']]]);
                    if (empty($result)) {
                        // Add citation
                        $this->Citation->create();
                        if ($this->Citation->save(['Citation' => $citation + ['updated' => date(DATE_ATOM)]])) {
                            $feedback[] = 'Citation for system ' . $id . ' added';
                            $cid = $this->Citation->id;
                        } else {
                            $feedback[] = 'Error adding citation => ' . debug($this->Citation->validationErrors);
                            //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                        }
                    } else {
                        $existing = true;
                        $cid = $result['Citation']['id'];
                    }
                } else {
                    $temp = $citation;
                    array_shift($temp); // You cannot do an array_shift inside an implode in PHP (causes error)
                    $citation['doi'] = implode("", $temp); // Make a unique id to match those refs that do not have a DOI (remove sysID first)
                    $result = $this->Citation->find('first', ['fields' => ['id'], 'conditions' => ['doi' => $citation['doi']]]);
                    if (empty($result)) {
                        // Add citation (no doi -> might be a duplicate)
                        $this->Citation->create();
                        if ($this->Citation->save(['Citation' => $citation + ['updated' => date(DATE_ATOM)]])) {
                            $feedback[] = 'Citation for system ' . $id . ' added';
                            $cid = $this->Citation->id;
                        } else {
                            $feedback[] = 'Error adding citation => ' . debug($this->Citation->validationErrors);
                            //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                        }
                    } else {
                        $existing = true;
                        $cid = $result['Citation']['id'];
                    }
                }

                // Add authors if the citation is new and they don't already exist
                $aid=$cid="";
                if (!$existing) {
                    foreach ($authors as $author) {
                        // Does author already exist?
                        $result = $this->Author->find('first', ['fields' => ['id'], 'conditions' => ['lastname' => $author['lastname'], 'firstname' => $author['firstname']]]);
                        if (empty($result)) {
                            $this->Author->create();
                            $author['updated'] = date(DATE_ATOM);
                            if ($this->Author->save(['Author' => $author])) {
                                $feedback[] = 'Author for system ' . $id . ' added';
                                $aid = $this->Author->id;
                            } else {
                                $feedback[] = 'Error adding author => ' . debug($this->Author->validationErrors);
                                //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                            }
                        } else {
                            $aid = $result['Author']['id'];
                        }
                        // Add link to join table
                        $this->AuthorsCitation->create();
                        $data = ['AuthorsCitation' => ['author_id' => $aid, 'citation_id' => $cid, 'updated' => date(DATE_ATOM)]];
                        if ($this->AuthorsCitation->save($data)) {
                            $feedback[] = 'Author/Citation join for system ' . $id . ' added';
                        } else {
                            $feedback[] = 'Error adding Author/Citation join => ' . debug($this->AuthorsCitation->validationErrors);
                            //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                        }
                    }
                }
            } else {
                $cid = "00000";
            }

            // System ($needs $cid)
            if ($this->System->save(['System' => $system['System'] + ['citation_id' => $cid]])) {
                $feedback[] = 'System ' . $id . ' updated';
            } else {
                $feedback[] = 'Error updating system => ' . debug($this->System->validationErrors);
                //echo "<pre>";print_r($feedback);echo "</pre>";exit;
            }

            // Chemicals
            foreach ($system['Chemical'] as $chemical) {
                $fields = explode("**", $chemical); // name => $fields[0], formula => $fields[1], casrn => $fields[2]
                $inchi = $inchikey = "";
                // Get Inchi string
                if (isset($fields[2]) && $fields[2] != "") {
                    $temp = get_headers("http://cactus.nci.nih.gov/chemical/structure/" . $fields[2] . "/stdinchi");
                    if (stristr($temp[0], "200 OK")) {
                        $inchi = file_get_contents("http://cactus.nci.nih.gov/chemical/structure/" . $fields[2] . "/stdinchi");
                        $feedback[] = 'InChI ' . $inchi . ' found (1)';
                    } else {
                        $temp = get_headers("http://cactus.nci.nih.gov/chemical/structure/" . urlencode($fields[0]) . "/stdinchi");
                        if (stristr($temp[0], "200 OK")) {
                            $inchi = file_get_contents("http://cactus.nci.nih.gov/chemical/structure/" . urlencode($fields[0]) . "/stdinchi");
                            $feedback[] = 'InChI ' . $inchi . ' found (2)';
                        } else {
                            $feedback[] = 'InChI for system ' . $fields[0] . ' not found';
                        }
                    }
                }
                // Get Inchi key
                if (isset($fields[2]) && $fields[2] != "") {
                    $temp = get_headers("http://cactus.nci.nih.gov/chemical/structure/" . $fields[2] . "/stdinchikey");
                    if (stristr($temp[0], "200 OK")) {
                        $inchikey = file_get_contents("http://cactus.nci.nih.gov/chemical/structure/" . $fields[2] . "/stdinchikey");
                        $feedback[] = 'InChIkey ' . $inchikey . ' found (1)';
                    } else {
                        $temp = get_headers("http://cactus.nci.nih.gov/chemical/structure/" . urlencode($fields[0]) . "/stdinchikey");
                        if (stristr($temp[0], "200 OK")) {
                            $inchikey = file_get_contents("http://cactus.nci.nih.gov/chemical/structure/" . urlencode($fields[0]) . "/stdinchikey");
                            $feedback[] = 'InChIkey ' . $inchikey . ' found (2)';
                        } else {
                            $inchikey = "";
                            $feedback[] = 'InChIKey for system ' . $fields[0] . ' not found';
                        }
                    }
                }
                // Check to see if this inchi already exists in the chemicals table
                ($inchi != "") ? $result = $this->Chemical->find('first', ['fields' => ['id'], 'conditions' => ['inchi' => $inchi]]) : $result = [];
                if (empty($result)) {
                    // Add to the chemicals table
                    $data = ['name' => $fields[0], 'formula' => $fields[1], 'casrn' => $fields[2], 'inchi' => $inchi, 'inchikey' => str_replace("InChIKey=", "", $inchikey), 'updated' => date(DATE_ATOM)];
                    $this->Chemical->create();
                    if ($this->Chemical->save(['Chemical' => $data])) {
                        $feedback[] = 'Chemical for system ' . $id . ' added';
                        $cid = $this->Chemical->id;
                    } else {
                        $feedback[] = 'Error adding chemical => ' . debug($this->Chemical->validationErrors);
                        //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                    }
                } else {
                    $cid = $result['Chemical']['id'];
                }
                // Add link to join table
                $this->ChemicalsSystem->create();
                $data = ['ChemicalsSystem' => ['system_id' => $id, 'chemical_id' => $cid, 'updated' => date(DATE_ATOM)]];
                if ($this->ChemicalsSystem->save($data)) {
                    $feedback[] = 'Chemical/System join for system ' . $id . ' added';
                    $feedback[] = $data;
                } else {
                    $feedback[] = 'Error adding Chemical/System join => ' . debug($this->ChemicalsSystem->validationErrors);
                    //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                }
            }

            // Variables
            foreach ($system['Variable'] as $variable) {
                $var = ['system_id' => $id, 'updated' => date(DATE_ATOM)];
                list($var['name'], $var['bounds']) = explode(" = ", $variable);
                $data = ['Variable' => $var];
                $this->Variable->create();
                if ($this->Variable->save($data)) {
                    $feedback[] = 'Variable for system ' . $id . ' added';
                    $feedback[] = $data;
                } else {
                    $feedback[] = 'Error adding variable => ' . debug($this->Variable->validationErrors);
                    //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                }
            }

            // Tables
            foreach ($system['Table'] as $table) {
                if ($table == "[]") {
                    continue;
                }
                $data = ['system_id' => $id, 'content' => $table, 'updated' => date(DATE_ATOM)];
                $data = ['Table' => $data];
                $this->Table->create();
                if ($this->Table->save($data)) {
                    $feedback[] = 'Table for system ' . $id . ' added';
                    $feedback[] = $data;
                } else {
                    $feedback[] = 'Error adding table => ' . debug($this->Table->validationErrors);
                    //echo "<pre>";print_r($feedback);echo "</pre>";exit;
                }
            }

            // For view
            $systems[$id] = $system;
            unset($system);
        }
        $this->set('data', $systems);
        $this->set('feedback', $feedback);
    }

    /**
     * Deal with <sup> and <sub> and other HTML tags
     * @param $string
     * @return string
     */
    function clean($string)
    {
        $string = str_replace("<sup>", "^", $string); // Preserve superscripts
        $string = str_replace("<sub>", "_", $string); // Preserve superscripts
        return strip_tags(html_entity_decode($string)); // Get rid of extraneous HTML tags
    }

    /**
     * Convert HTML table into json string
     * @param $table
     * @return string
     */
    function table2json($table)
    {
        $tidy_config = ['clean' => true, 'output-xhtml' => true, 'show-body-only' => true, 'wrap' => 0];
        $tidy = new tidy();
        $table = $tidy->repairString($table, $tidy_config, 'UTF8'); // Clean up HTML
        $table = str_replace([" class=\"c1\"", "\n"], "", $table); // Remove attributes and newlines
        $rows = array_filter(explode("</tr><tr>", $table)); // Create array of rows
        foreach ($rows as $key => $row) {
            $row = str_replace("**", "^", $row); // Used to indicate superscript
            $row = str_replace("</th><th>", "**", $row); // Separate table header cells
            $row = str_replace("</td><td>", "**", $row); // Separate table row cells
            $row = str_replace("<sup>", "^", $row); // Preserve superscripts
            $row = str_replace("<sub>", "_", $row); // Preserve subscripts
            $row = strip_tags(html_entity_decode($row)); // Get rid of extraneous HTML tags
            $rows[$key] = explode("**", $row); // Create array of row data
        }
        return json_encode($rows); // Create json string of table array
    }

    /**
     * Convert XML response into array
     * @param $response
     * @return array
     */
    function xmlToArray($response)
    {
        $array = Xml::toArray(Xml::build($response));
        return $array;
    }
}