<?php
/**
 * XHTML sanitizer for %MediaWiki.
 *
 * Copyright © 2002-2005 Brion Vibber <brion@pobox.com> et al
 * http://www.mediawiki.org/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Parser
 */

/**
 * XHTML sanitizer for MediaWiki
 * @ingroup Parser
 */
class Sanitizer
{
    /**
     * Regular expression to match various types of character references in
     * Sanitizer::normalizeCharReferences and Sanitizer::decodeCharReferences
     */
    public const CHAR_REFS_REGEX =
        '/&([A-Za-z0-9\x80-\xff]+);
		 |&\#([0-9]+);
		 |&\#[xX]([0-9A-Fa-f]+);
		 |(&)/x';

    /**
     * Blacklist for evil uris like javascript:
     * WARNING: DO NOT use this in any place that actually requires blacklisting
     * for security reasons. There are NUMEROUS[1] ways to bypass blacklisting, the
     * only way to be secure from javascript: uri based xss vectors is to whitelist
     * things that you know are safe and deny everything else.
     * [1]: http://ha.ckers.org/xss.html
     */
    public const EVIL_URI_PATTERN = '!(^|\s|\*/\s*)(javascript|vbscript)([^\w]|$)!i';

    public const XMLNS_ATTRIBUTE_PATTERN = '/^xmlns:[:A-Z_a-z-.0-9]+$/';

    /**
     * List of all named character entities defined in HTML 4.01
     * http://www.w3.org/TR/html4/sgml/entities.html
     * As well as &apos; which is only defined starting in XHTML1.
     * @private
     */
    public static $htmlEntities = [
        'Aacute' => 193,
        'aacute' => 225,
        'Acirc' => 194,
        'acirc' => 226,
        'acute' => 180,
        'AElig' => 198,
        'aelig' => 230,
        'Agrave' => 192,
        'agrave' => 224,
        'alefsym' => 8501,
        'Alpha' => 913,
        'alpha' => 945,
        'amp' => 38,
        'and' => 8743,
        'ang' => 8736,
        'apos' => 39, // New in XHTML & HTML 5; avoid in output for compatibility with IE.
        'Aring' => 197,
        'aring' => 229,
        'asymp' => 8776,
        'Atilde' => 195,
        'atilde' => 227,
        'Auml' => 196,
        'auml' => 228,
        'bdquo' => 8222,
        'Beta' => 914,
        'beta' => 946,
        'brvbar' => 166,
        'bull' => 8226,
        'cap' => 8745,
        'Ccedil' => 199,
        'ccedil' => 231,
        'cedil' => 184,
        'cent' => 162,
        'Chi' => 935,
        'chi' => 967,
        'circ' => 710,
        'clubs' => 9827,
        'cong' => 8773,
        'copy' => 169,
        'crarr' => 8629,
        'cup' => 8746,
        'curren' => 164,
        'dagger' => 8224,
        'Dagger' => 8225,
        'darr' => 8595,
        'dArr' => 8659,
        'deg' => 176,
        'Delta' => 916,
        'delta' => 948,
        'diams' => 9830,
        'divide' => 247,
        'Eacute' => 201,
        'eacute' => 233,
        'Ecirc' => 202,
        'ecirc' => 234,
        'Egrave' => 200,
        'egrave' => 232,
        'empty' => 8709,
        'emsp' => 8195,
        'ensp' => 8194,
        'Epsilon' => 917,
        'epsilon' => 949,
        'equiv' => 8801,
        'Eta' => 919,
        'eta' => 951,
        'ETH' => 208,
        'eth' => 240,
        'Euml' => 203,
        'euml' => 235,
        'euro' => 8364,
        'exist' => 8707,
        'fnof' => 402,
        'forall' => 8704,
        'frac12' => 189,
        'frac14' => 188,
        'frac34' => 190,
        'frasl' => 8260,
        'Gamma' => 915,
        'gamma' => 947,
        'ge' => 8805,
        'gt' => 62,
        'harr' => 8596,
        'hArr' => 8660,
        'hearts' => 9829,
        'hellip' => 8230,
        'Iacute' => 205,
        'iacute' => 237,
        'Icirc' => 206,
        'icirc' => 238,
        'iexcl' => 161,
        'Igrave' => 204,
        'igrave' => 236,
        'image' => 8465,
        'infin' => 8734,
        'int' => 8747,
        'Iota' => 921,
        'iota' => 953,
        'iquest' => 191,
        'isin' => 8712,
        'Iuml' => 207,
        'iuml' => 239,
        'Kappa' => 922,
        'kappa' => 954,
        'Lambda' => 923,
        'lambda' => 955,
        'lang' => 9001,
        'laquo' => 171,
        'larr' => 8592,
        'lArr' => 8656,
        'lceil' => 8968,
        'ldquo' => 8220,
        'le' => 8804,
        'lfloor' => 8970,
        'lowast' => 8727,
        'loz' => 9674,
        'lrm' => 8206,
        'lsaquo' => 8249,
        'lsquo' => 8216,
        'lt' => 60,
        'macr' => 175,
        'mdash' => 8212,
        'micro' => 181,
        'middot' => 183,
        'minus' => 8722,
        'Mu' => 924,
        'mu' => 956,
        'nabla' => 8711,
        'nbsp' => 160,
        'ndash' => 8211,
        'ne' => 8800,
        'ni' => 8715,
        'not' => 172,
        'notin' => 8713,
        'nsub' => 8836,
        'Ntilde' => 209,
        'ntilde' => 241,
        'Nu' => 925,
        'nu' => 957,
        'Oacute' => 211,
        'oacute' => 243,
        'Ocirc' => 212,
        'ocirc' => 244,
        'OElig' => 338,
        'oelig' => 339,
        'Ograve' => 210,
        'ograve' => 242,
        'oline' => 8254,
        'Omega' => 937,
        'omega' => 969,
        'Omicron' => 927,
        'omicron' => 959,
        'oplus' => 8853,
        'or' => 8744,
        'ordf' => 170,
        'ordm' => 186,
        'Oslash' => 216,
        'oslash' => 248,
        'Otilde' => 213,
        'otilde' => 245,
        'otimes' => 8855,
        'Ouml' => 214,
        'ouml' => 246,
        'para' => 182,
        'part' => 8706,
        'permil' => 8240,
        'perp' => 8869,
        'Phi' => 934,
        'phi' => 966,
        'Pi' => 928,
        'pi' => 960,
        'piv' => 982,
        'plusmn' => 177,
        'pound' => 163,
        'prime' => 8242,
        'Prime' => 8243,
        'prod' => 8719,
        'prop' => 8733,
        'Psi' => 936,
        'psi' => 968,
        'quot' => 34,
        'radic' => 8730,
        'rang' => 9002,
        'raquo' => 187,
        'rarr' => 8594,
        'rArr' => 8658,
        'rceil' => 8969,
        'rdquo' => 8221,
        'real' => 8476,
        'reg' => 174,
        'rfloor' => 8971,
        'Rho' => 929,
        'rho' => 961,
        'rlm' => 8207,
        'rsaquo' => 8250,
        'rsquo' => 8217,
        'sbquo' => 8218,
        'Scaron' => 352,
        'scaron' => 353,
        'sdot' => 8901,
        'sect' => 167,
        'shy' => 173,
        'Sigma' => 931,
        'sigma' => 963,
        'sigmaf' => 962,
        'sim' => 8764,
        'spades' => 9824,
        'sub' => 8834,
        'sube' => 8838,
        'sum' => 8721,
        'sup' => 8835,
        'sup1' => 185,
        'sup2' => 178,
        'sup3' => 179,
        'supe' => 8839,
        'szlig' => 223,
        'Tau' => 932,
        'tau' => 964,
        'there4' => 8756,
        'Theta' => 920,
        'theta' => 952,
        'thetasym' => 977,
        'thinsp' => 8201,
        'THORN' => 222,
        'thorn' => 254,
        'tilde' => 732,
        'times' => 215,
        'trade' => 8482,
        'Uacute' => 218,
        'uacute' => 250,
        'uarr' => 8593,
        'uArr' => 8657,
        'Ucirc' => 219,
        'ucirc' => 251,
        'Ugrave' => 217,
        'ugrave' => 249,
        'uml' => 168,
        'upsih' => 978,
        'Upsilon' => 933,
        'upsilon' => 965,
        'Uuml' => 220,
        'uuml' => 252,
        'weierp' => 8472,
        'Xi' => 926,
        'xi' => 958,
        'Yacute' => 221,
        'yacute' => 253,
        'yen' => 165,
        'Yuml' => 376,
        'yuml' => 255,
        'Zeta' => 918,
        'zeta' => 950,
        'zwj' => 8205,
        'zwnj' => 8204,
    ];

    /**
     * Character entity aliases accepted by MediaWiki
     */
    public static $htmlEntityAliases = [
        'רלמ' => 'rlm',
        'رلم' => 'rlm',
    ];

    /**
     * Lazy-initialised attributes regex, see getAttribsRegex()
     */
    public static $attribsRegex;

    /**
     * Regular expression to match HTML/XML attribute pairs within a tag.
     * Allows some... latitude.
     * Used in Sanitizer::fixTagAttributes and Sanitizer::decodeTagAttributes
     */
    public static function getAttribsRegex()
    {
        if (self::$attribsRegex === null) {
            $attribFirst = '[:A-Z_a-z0-9]';
            $attrib = '[:A-Z_a-z-.0-9]';
            $space = '[\x09\x0a\x0d\x20]';
            self::$attribsRegex =
                "/(?:^|$space)({$attribFirst}{$attrib}*)
				  ($space*=$space*
					(?:
					 # The attribute value: quoted or alone
					  \"([^<\"]*)\"
					 | '([^<']*)'
					 |  ([a-zA-Z0-9!#$%&()*,\\-.\\/:;<>?@[\\]^_`{|}~]+)
					 |  (\#[0-9a-fA-F]+) # Technically wrong, but lots of
										 # colors are specified like this.
										 # We'll be normalizing it.
					)
				)?(?=$space|\$)/sx";
        }
        return self::$attribsRegex;
    }

    /**
     * Cleans up HTML, removes dangerous tags and attributes, and
     * removes HTML comments
     * @private
     * @param $text String
     * @param $processCallback Callback to do any variable or parameter replacements in HTML attribute values
     * @param array $args for the processing callback
     * @param array $extratags for any extra tags to include
     * @param array $removetags for any tags (default or extra) to exclude
     * @return string
     */
    public static function removeHTMLtags($text, $processCallback = null, $args = [], $extratags = [], $removetags = [])
    {
        global $wgUseTidy, $wgHtml5, $wgAllowMicrodataAttributes, $wgAllowImageTag;

        static $htmlpairsStatic, $htmlsingle, $htmlsingleonly, $htmlnest, $tabletags,
        $htmllist, $listtags, $htmlsingleallowed, $htmlelementsStatic, $staticInitialised;

        wfProfileIn(__METHOD__);

        // Base our staticInitialised variable off of the global config state so that if the globals
        // are changed (like in the screwed up test system) we will re-initialise the settings.
        $globalContext = implode('-', compact('wgHtml5', 'wgAllowMicrodataAttributes', 'wgAllowImageTag'));
        if (! $staticInitialised || $staticInitialised != $globalContext) {
            $htmlpairsStatic = [ # Tags that must be closed
                'b', 'bdi', 'del', 'i', 'ins', 'u', 'font', 'big', 'small', 'sub', 'sup', 'h1',
                'h2', 'h3', 'h4', 'h5', 'h6', 'cite', 'code', 'em', 's',
                'strike', 'strong', 'tt', 'var', 'div', 'center',
                'blockquote', 'ol', 'ul', 'dl', 'table', 'caption', 'pre',
                'ruby', 'rt', 'rb', 'rp', 'p', 'span', 'abbr', 'dfn',
                'kbd', 'samp',
            ];
            if ($wgHtml5) {
                $htmlpairsStatic = array_merge($htmlpairsStatic, ['data', 'time', 'mark']);
            }
            $htmlsingle = [
                'br', 'hr', 'li', 'dt', 'dd',
            ];
            $htmlsingleonly = [ # Elements that cannot have close tags
                'br', 'hr',
            ];
            if ($wgHtml5 && $wgAllowMicrodataAttributes) {
                $htmlsingle[] = $htmlsingleonly[] = 'meta';
                $htmlsingle[] = $htmlsingleonly[] = 'link';
            }
            $htmlnest = [ # Tags that can be nested--??
                'table', 'tr', 'td', 'th', 'div', 'blockquote', 'ol', 'ul',
                'li', 'dl', 'dt', 'dd', 'font', 'big', 'small', 'sub', 'sup', 'span',
            ];
            $tabletags = [ # Can only appear inside table, we will close them
                'td', 'th', 'tr',
            ];
            $htmllist = [ # Tags used by list
                'ul', 'ol',
            ];
            $listtags = [ # Tags that can appear in a list
                'li',
            ];

            if ($wgAllowImageTag) {
                $htmlsingle[] = 'img';
                $htmlsingleonly[] = 'img';
            }

            $htmlsingleallowed = array_unique(array_merge($htmlsingle, $tabletags));
            $htmlelementsStatic = array_unique(array_merge($htmlsingle, $htmlpairsStatic, $htmlnest));

            # Convert them all to hashtables for faster lookup
            $vars = ['htmlpairsStatic', 'htmlsingle', 'htmlsingleonly', 'htmlnest', 'tabletags',
                'htmllist', 'listtags', 'htmlsingleallowed', 'htmlelementsStatic'];
            foreach ($vars as $var) {
                $$var = array_flip($$var);
            }
            $staticInitialised = $globalContext;
        }
        # Populate $htmlpairs and $htmlelements with the $extratags and $removetags arrays
        $extratags = array_flip($extratags);
        $removetags = array_flip($removetags);
        $htmlpairs = array_merge($extratags, $htmlpairsStatic);
        $htmlelements = array_diff_key(array_merge($extratags, $htmlelementsStatic), $removetags);

        # Remove HTML comments
        $text = Sanitizer::removeHTMLcomments($text);
        $bits = explode('<', $text);
        $text = str_replace('>', '&gt;', array_shift($bits));
        if (! $wgUseTidy) {
            $tagstack = $tablestack = [];
            foreach ($bits as $x) {
                $regs = [];
                # $slash: Does the current element start with a '/'?
                # $t: Current element name
                # $params: String between element name and >
                # $brace: Ending '>' or '/>'
                # $rest: Everything until the next element of $bits
                if (preg_match('!^(/?)(\\w+)([^>]*?)(/{0,1}>)([^<]*)$!', $x, $regs)) {
                    list( /* $qbar */, $slash, $t, $params, $brace, $rest) = $regs;
                } else {
                    $slash = $t = $params = $brace = $rest = null;
                }

                $badtag = false;
                if (isset($htmlelements[$t = strtolower($t)])) {
                    # Check our stack
                    if ($slash && isset($htmlsingleonly[$t])) {
                        $badtag = true;
                    } elseif ($slash) {
                        # Closing a tag... is it the one we just opened?
                        $ot = @array_pop($tagstack);
                        if ($ot != $t) {
                            if (isset($htmlsingleallowed[$ot])) {
                                # Pop all elements with an optional close tag
                                # and see if we find a match below them
                                $optstack = [];
                                array_push($optstack, $ot);
                                wfSuppressWarnings();
                                $ot = array_pop($tagstack);
                                wfRestoreWarnings();
                                while ($ot != $t && isset($htmlsingleallowed[$ot])) {
                                    array_push($optstack, $ot);
                                    wfSuppressWarnings();
                                    $ot = array_pop($tagstack);
                                    wfRestoreWarnings();
                                }
                                if ($t != $ot) {
                                    # No match. Push the optional elements back again
                                    $badtag = true;
                                    wfSuppressWarnings();
                                    $ot = array_pop($optstack);
                                    wfRestoreWarnings();
                                    while ($ot) {
                                        array_push($tagstack, $ot);
                                        wfSuppressWarnings();
                                        $ot = array_pop($optstack);
                                        wfRestoreWarnings();
                                    }
                                }
                            } else {
                                @array_push($tagstack, $ot);
                                # <li> can be nested in <ul> or <ol>, skip those cases:
                                if (! isset($htmllist[$ot]) || ! isset($listtags[$t])) {
                                    $badtag = true;
                                }
                            }
                        } else {
                            if ($t == 'table') {
                                $tagstack = array_pop($tablestack);
                            }
                        }
                        $newparams = '';
                    } else {
                        # Keep track for later
                        if (isset($tabletags[$t]) &&
                        ! in_array('table', $tagstack)) {
                            $badtag = true;
                        } elseif (in_array($t, $tagstack) &&
                        ! isset($htmlnest[$t])) {
                            $badtag = true;
                            # Is it a self closed htmlpair ? (bug 5487)
                        } elseif ($brace == '/>' &&
                        isset($htmlpairs[$t])) {
                            $badtag = true;
                        } elseif (isset($htmlsingleonly[$t])) {
                            # Hack to force empty tag for unclosable elements
                            $brace = '/>';
                        } elseif (isset($htmlsingle[$t])) {
                            # Hack to not close $htmlsingle tags
                            $brace = null;
                            # Still need to push this optionally-closed tag to
                            # the tag stack so that we can match end tags
                            # instead of marking them as bad.
                            array_push($tagstack, $t);
                        } elseif (isset($tabletags[$t])
                        && in_array($t, $tagstack)) {
                            // New table tag but forgot to close the previous one
                            $text .= "</$t>";
                        } else {
                            if ($t == 'table') {
                                array_push($tablestack, $tagstack);
                                $tagstack = [];
                            }
                            array_push($tagstack, $t);
                        }

                        # Replace any variables or template parameters with
                        # plaintext results.
                        if (is_callable($processCallback)) {
                            call_user_func_array($processCallback, [&$params, $args]);
                        }

                        if (! Sanitizer::validateTag($params, $t)) {
                            $badtag = true;
                        }

                        # Strip non-approved attributes from the tag
                        $newparams = Sanitizer::fixTagAttributes($params, $t);
                    }
                    if (! $badtag) {
                        $rest = str_replace('>', '&gt;', $rest);
                        $close = ($brace == '/>' && ! $slash) ? ' /' : '';
                        $text .= "<$slash$t$newparams$close>$rest";
                        continue;
                    }
                }
                $text .= '&lt;' . str_replace('>', '&gt;', $x);
            }
            # Close off any remaining tags
            while (is_array($tagstack) && ($t = array_pop($tagstack))) {
                $text .= "</$t>\n";
                if ($t == 'table') {
                    $tagstack = array_pop($tablestack);
                }
            }
        } else {
            # this might be possible using tidy itself
            foreach ($bits as $x) {
                preg_match(
                    '/^(\\/?)(\\w+)([^>]*?)(\\/{0,1}>)([^<]*)$/',
                    $x,
                    $regs
                );
                @list( /* $qbar */, $slash, $t, $params, $brace, $rest) = $regs;
                $badtag = false;
                if (isset($htmlelements[$t = strtolower($t)])) {
                    if (is_callable($processCallback)) {
                        call_user_func_array($processCallback, [&$params, $args]);
                    }

                    if (! Sanitizer::validateTag($params, $t)) {
                        $badtag = true;
                    }

                    $newparams = Sanitizer::fixTagAttributes($params, $t);
                    if (! $badtag) {
                        $rest = str_replace('>', '&gt;', $rest);
                        $text .= "<$slash$t$newparams$brace$rest";
                        continue;
                    }
                }
                $text .= '&lt;' . str_replace('>', '&gt;', $x);
            }
        }
        wfProfileOut(__METHOD__);
        return $text;
    }

    /**
     * Remove '<!--', '-->', and everything between.
     * To avoid leaving blank lines, when a comment is both preceded
     * and followed by a newline (ignoring spaces), trim leading and
     * trailing spaces and one of the newlines.
     *
     * @private
     * @param $text String
     * @return string
     */
    public static function removeHTMLcomments($text)
    {
        wfProfileIn(__METHOD__);
        while (($start = strpos($text, '<!--')) !== false) {
            $end = strpos($text, '-->', $start + 4);
            if ($end === false) {
                # Unterminated comment; bail out
                break;
            }

            $end += 3;

            # Trim space and newline if the comment is both
            # preceded and followed by a newline
            $spaceStart = max($start - 1, 0);
            $spaceLen = $end - $spaceStart;
            while (substr($text, $spaceStart, 1) === ' ' && $spaceStart > 0) {
                $spaceStart--;
                $spaceLen++;
            }
            while (substr($text, $spaceStart + $spaceLen, 1) === ' ') {
                $spaceLen++;
            }
            if (substr($text, $spaceStart, 1) === "\n" and substr($text, $spaceStart + $spaceLen, 1) === "\n") {
                # Remove the comment, leading and trailing
                # spaces, and leave only one newline.
                $text = substr_replace($text, "\n", $spaceStart, $spaceLen + 1);
            } else {
                # Remove just the comment.
                $text = substr_replace($text, '', $start, $end - $start);
            }
        }
        wfProfileOut(__METHOD__);
        return $text;
    }

    /**
     * Takes attribute names and values for a tag and the tag name and
     * validates that the tag is allowed to be present.
     * This DOES NOT validate the attributes, nor does it validate the
     * tags themselves. This method only handles the special circumstances
     * where we may want to allow a tag within content but ONLY when it has
     * specific attributes set.
     *
     * @return bool
     */
    public static function validateTag($params, $element)
    {
        $params = Sanitizer::decodeTagAttributes($params);

        if ($element == 'meta' || $element == 'link') {
            if (! isset($params['itemprop'])) {
                // <meta> and <link> must have an itemprop="" otherwise they are not valid or safe in content
                return false;
            }
            if ($element == 'meta' && ! isset($params['content'])) {
                // <meta> must have a content="" for the itemprop
                return false;
            }
            if ($element == 'link' && ! isset($params['href'])) {
                // <link> must have an associated href=""
                return false;
            }
        }

        return true;
    }

    /**
     * Take an array of attribute names and values and normalize or discard
     * illegal values for the given element type.
     *
     * - Discards attributes not on a whitelist for the given element
     * - Unsafe style attributes are discarded
     * - Invalid id attributes are re-encoded
     *
     * @param $attribs Array
     * @param $element String
     * @return array
     *
     * @todo Check for legal values where the DTD limits things.
     * @todo Check for unique id attribute :P
     */
    public static function validateTagAttributes($attribs, $element)
    {
        return Sanitizer::validateAttributes(
            $attribs,
            Sanitizer::attributeWhitelist($element)
        );
    }

    /**
     * Take an array of attribute names and values and normalize or discard
     * illegal values for the given whitelist.
     *
     * - Discards attributes not the given whitelist
     * - Unsafe style attributes are discarded
     * - Invalid id attributes are re-encoded
     *
     * @param $attribs Array
     * @param array $whitelist list of allowed attribute names
     * @return array
     *
     * @todo Check for legal values where the DTD limits things.
     * @todo Check for unique id attribute :P
     */
    public static function validateAttributes($attribs, $whitelist)
    {
        global $wgAllowRdfaAttributes, $wgAllowMicrodataAttributes, $wgHtml5;

        $whitelist = array_flip($whitelist);
        $hrefExp = '/^(https?)[^\s]+$/';

        $out = [];
        foreach ($attribs as $attribute => $value) {
            #allow XML namespace declaration if RDFa is enabled
            if ($wgAllowRdfaAttributes && preg_match(self::XMLNS_ATTRIBUTE_PATTERN, $attribute)) {
                if (! preg_match(self::EVIL_URI_PATTERN, $value)) {
                    $out[$attribute] = $value;
                }

                continue;
            }

            # Allow any attribute beginning with "data-", if in HTML5 mode
            if (! ($wgHtml5 && preg_match('/^data-/i', $attribute)) && ! isset($whitelist[$attribute])) {
                continue;
            }

            # Strip javascript "expression" from stylesheets.
            # http://msdn.microsoft.com/workshop/author/dhtml/overview/recalc.asp
            if ($attribute == 'style') {
                $value = Sanitizer::checkCss($value);
            }

            if ($attribute === 'id') {
                $value = Sanitizer::escapeId($value, 'noninitial');
            }

            # WAI-ARIA
            # http://www.w3.org/TR/wai-aria/
            # http://www.whatwg.org/specs/web-apps/current-work/multipage/elements.html#wai-aria
            # For now we only support role="presentation" until we work out what roles should be
            # usable by content and we ensure that our code explicitly rejects patterns that
            # violate HTML5's ARIA restrictions.
            if ($attribute === 'role' && $value !== 'presentation') {
                continue;
            }

            //RDFa and microdata properties allow URLs, URIs and/or CURIs. check them for sanity
            if ($attribute === 'rel' || $attribute === 'rev' ||
                $attribute === 'about' || $attribute === 'property' || $attribute === 'resource' || #RDFa
                $attribute === 'datatype' || $attribute === 'typeof' ||                             #RDFa
                $attribute === 'itemid' || $attribute === 'itemprop' || $attribute === 'itemref' || #HTML5 microdata
                $attribute === 'itemscope' || $attribute === 'itemtype') {                         #HTML5 microdata
                //Paranoia. Allow "simple" values but suppress javascript
                if (preg_match(self::EVIL_URI_PATTERN, $value)) {
                    continue;
                }
            }

            # NOTE: even though elements using href/src are not allowed directly, supply
            #       validation code that can be used by tag hook handlers, etc
            if ($attribute === 'href' || $attribute === 'src') {
                if (! preg_match($hrefExp, $value)) {
                    continue; //drop any href or src attributes not using an allowed protocol.
                    //NOTE: this also drops all relative URLs
                }
            }

            // If this attribute was previously set, override it.
            // Output should only have one attribute of each name.
            $out[$attribute] = $value;
        }

        if ($wgAllowMicrodataAttributes) {
            # itemtype, itemid, itemref don't make sense without itemscope
            if (! array_key_exists('itemscope', $out)) {
                unset($out['itemtype']);
                unset($out['itemid']);
                unset($out['itemref']);
            }
            # TODO: Strip itemprop if we aren't descendants of an itemscope or pointed to by an itemref.
        }
        return $out;
    }

    /**
     * Merge two sets of HTML attributes.  Conflicting items in the second set
     * will override those in the first, except for 'class' attributes which
     * will be combined (if they're both strings).
     *
     * @todo implement merging for other attributes such as style
     * @param $a Array
     * @param $b Array
     * @return array
     */
    public static function mergeAttributes($a, $b)
    {
        $out = array_merge($a, $b);
        if (isset($a['class']) && isset($b['class'])
        && is_string($a['class']) && is_string($b['class'])
        && $a['class'] !== $b['class']) {
            $classes = preg_split(
                '/\s+/',
                "{$a['class']} {$b['class']}",
                -1,
                PREG_SPLIT_NO_EMPTY
            );
            $out['class'] = implode(' ', array_unique($classes));
        }
        return $out;
    }

    /**
     * Pick apart some CSS and check it for forbidden or unsafe structures.
     * Returns a sanitized string. This sanitized string will have
     * character references and escape sequences decoded, and comments
     * stripped. If the input is just too evil, only a comment complaining
     * about evilness will be returned.
     *
     * Currently URL references, 'expression', 'tps' are forbidden.
     *
     * NOTE: Despite the fact that character references are decoded, the
     * returned string may contain character references given certain
     * clever input strings. These character references must
     * be escaped before the return value is embedded in HTML.
     *
     * @param $value String
     * @return string
     */
    public static function checkCss($value)
    {
        // Decode character references like &#123;
        $value = Sanitizer::decodeCharReferences($value);

        // Decode escape sequences and line continuation
        // See the grammar in the CSS 2 spec, appendix D.
        // This has to be done AFTER decoding character references.
        // This means it isn't possible for this function to return
        // unsanitized escape sequences. It is possible to manufacture
        // input that contains character references that decode to
        // escape sequences that decode to character references, but
        // it's OK for the return value to contain character references
        // because the caller is supposed to escape those anyway.
        static $decodeRegex;
        if (! $decodeRegex) {
            $space = '[\\x20\\t\\r\\n\\f]';
            $nl = '(?:\\n|\\r\\n|\\r|\\f)';
            $backslash = '\\\\';
            $decodeRegex = "/ $backslash
				(?:
					($nl) |  # 1. Line continuation
					([0-9A-Fa-f]{1,6})$space? |  # 2. character number
					(.) | # 3. backslash cancelling special meaning
					() | # 4. backslash at end of string
				)/xu";
        }
        $value = preg_replace_callback(
            $decodeRegex,
            [__CLASS__, 'cssDecodeCallback'],
            $value
        );

        // Remove any comments; IE gets token splitting wrong
        // This must be done AFTER decoding character references and
        // escape sequences, because those steps can introduce comments
        // This step cannot introduce character references or escape
        // sequences, because it replaces comments with spaces rather
        // than removing them completely.
        $value = StringUtils::delimiterReplace('/*', '*/', ' ', $value);

        // Remove anything after a comment-start token, to guard against
        // incorrect client implementations.
        $commentPos = strpos($value, '/*');
        if ($commentPos !== false) {
            $value = substr($value, 0, $commentPos);
        }

        // Reject problematic keywords and control characters
        if (preg_match('/[\000-\010\016-\037\177]/', $value)) {
            return '/* invalid control char */';
        } elseif (preg_match('! expression | filter\s*: | accelerator\s*: | url\s*\( | image\s*\( | image-set\s*\( !ix', $value)) {
            return '/* insecure input */';
        }
        return $value;
    }

    /**
     * @param $matches array
     * @return string
     */
    public static function cssDecodeCallback($matches)
    {
        if ($matches[1] !== '') {
            // Line continuation
            return '';
        } elseif ($matches[2] !== '') {
            $char = codepointToUtf8(hexdec($matches[2]));
        } elseif ($matches[3] !== '') {
            $char = $matches[3];
        } else {
            $char = '\\';
        }
        if ($char == "\n" || $char == '"' || $char == "'" || $char == '\\') {
            // These characters need to be escaped in strings
            // Clean up the escape sequence to avoid parsing errors by clients
            return '\\' . dechex(ord($char)) . ' ';
        } else {
            // Decode unnecessary escape
            return $char;
        }
    }

    /**
     * Take a tag soup fragment listing an HTML element's attributes
     * and normalize it to well-formed XML, discarding unwanted attributes.
     * Output is safe for further wikitext processing, with escaping of
     * values that could trigger problems.
     *
     * - Normalizes attribute names to lowercase
     * - Discards attributes not on a whitelist for the given element
     * - Turns broken or invalid entities into plaintext
     * - Double-quotes all attribute values
     * - Attributes without values are given the name as attribute
     * - Double attributes are discarded
     * - Unsafe style attributes are discarded
     * - Prepends space if there are attributes.
     *
     * @param $text String
     * @param $element String
     * @return string
     */
    public static function fixTagAttributes($text, $element)
    {
        if (trim($text) == '') {
            return '';
        }

        $decoded = Sanitizer::decodeTagAttributes($text);
        $stripped = Sanitizer::validateTagAttributes($decoded, $element);

        $attribs = [];
        foreach ($stripped as $attribute => $value) {
            $encAttribute = htmlspecialchars($attribute);
            $encValue = Sanitizer::safeEncodeAttribute($value);

            $attribs[] = "$encAttribute=\"$encValue\"";
        }
        return count($attribs) ? ' ' . implode(' ', $attribs) : '';
    }

    /**
     * Encode an attribute value for HTML output.
     * @param $text String
     * @return HTML-encoded text fragment
     */
    public static function encodeAttribute($text)
    {
        $encValue = htmlspecialchars($text, ENT_QUOTES);

        // Whitespace is normalized during attribute decoding,
        // so if we've been passed non-spaces we must encode them
        // ahead of time or they won't be preserved.
        $encValue = strtr($encValue, [
            "\n" => '&#10;',
            "\r" => '&#13;',
            "\t" => '&#9;',
        ]);

        return $encValue;
    }

    /**
     * Encode an attribute value for HTML tags, with extra armoring
     * against further wiki processing.
     * @param $text String
     * @return HTML-encoded text fragment
     */
    public static function safeEncodeAttribute($text)
    {
        $encValue = Sanitizer::encodeAttribute($text);

        # Templates and links may be expanded in later parsing,
        # creating invalid or dangerous output. Suppress this.
        $encValue = strtr($encValue, [
            '<' => '&lt;',   // This should never happen,
            '>' => '&gt;',   // we've received invalid input
            '"' => '&quot;', // which should have been escaped.
            '{' => '&#123;',
            '[' => '&#91;',
            "''" => '&#39;&#39;',
            'ISBN' => '&#73;SBN',
            'RFC' => '&#82;FC',
            'PMID' => '&#80;MID',
            '|' => '&#124;',
            '__' => '&#95;_',
        ]);

        # Stupid hack
        $encValue = preg_replace_callback(
            '/((?i)https?)/',
            ['Sanitizer', 'armorLinksCallback'],
            $encValue
        );
        return $encValue;
    }

    /**
     * Given a value, escape it so that it can be used in an id attribute and
     * return it.  This will use HTML5 validation if $wgExperimentalHtmlIds is
     * true, allowing anything but ASCII whitespace.  Otherwise it will use
     * HTML 4 rules, which means a narrow subset of ASCII, with bad characters
     * escaped with lots of dots.
     *
     * To ensure we don't have to bother escaping anything, we also strip ', ",
     * & even if $wgExperimentalIds is true.  TODO: Is this the best tactic?
     * We also strip # because it upsets IE, and % because it could be
     * ambiguous if it's part of something that looks like a percent escape
     * (which don't work reliably in fragments cross-browser).
     *
     * @see http://www.w3.org/TR/html401/types.html#type-name Valid characters
     *                                                          in the id and
     *                                                          name attributes
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.2.3 Anchors with the id attribute
     * @see http://www.whatwg.org/specs/web-apps/current-work/multipage/elements.html#the-id-attribute
     *   HTML5 definition of id attribute
     *
     * @param string $id id to escape
     * @param $options Mixed: string or array of strings (default is array()):
     *   'noninitial': This is a non-initial fragment of an id, not a full id,
     *       so don't pay attention if the first character isn't valid at the
     *       beginning of an id.  Only matters if $wgExperimentalHtmlIds is
     *       false.
     *   'legacy': Behave the way the old HTML 4-based ID escaping worked even
     *       if $wgExperimentalHtmlIds is used, so we can generate extra
     *       anchors and links won't break.
     * @return string
     */
    public static function escapeId($id, $options = [])
    {
        global $wgHtml5, $wgExperimentalHtmlIds;
        $options = (array) $options;

        if ($wgHtml5 && $wgExperimentalHtmlIds && ! in_array('legacy', $options)) {
            $id = Sanitizer::decodeCharReferences($id);
            $id = preg_replace('/[ \t\n\r\f_\'"&#%]+/', '_', $id);
            $id = trim($id, '_');
            if ($id === '') {
                # Must have been all whitespace to start with.
                return '_';
            } else {
                return $id;
            }
        }

        # HTML4-style escaping
        static $replace = [
            '%3A' => ':',
            '%' => '.',
        ];

        $id = urlencode(Sanitizer::decodeCharReferences(strtr($id, ' ', '_')));
        $id = str_replace(array_keys($replace), array_values($replace), $id);

        if (! preg_match('/^[a-zA-Z]/', $id)
        && ! in_array('noninitial', $options)) {
            // Initial character must be a letter!
            $id = "x$id";
        }
        return $id;
    }

    /**
     * Given a value, escape it so that it can be used as a CSS class and
     * return it.
     *
     * @todo For extra validity, input should be validated UTF-8.
     *
     * @see http://www.w3.org/TR/CSS21/syndata.html Valid characters/format
     *
     * @param $class String
     * @return string
     */
    public static function escapeClass($class)
    {
        // Convert ugly stuff to underscores and kill underscores in ugly places
        return rtrim(preg_replace(
            ['/(^[0-9\\-])|[\\x00-\\x20!"#$%&\'()*+,.\\/:;<=>?@[\\]^`{|}~]|\\xC2\\xA0/', '/_+/'],
            '_',
            $class
        ), '_');
    }

    /**
     * Given HTML input, escape with htmlspecialchars but un-escape entities.
     * This allows (generally harmless) entities like &#160; to survive.
     *
     * @param string $html to escape
     * @return String: escaped input
     */
    public static function escapeHtmlAllowEntities($html)
    {
        $html = Sanitizer::decodeCharReferences($html);
        # It seems wise to escape ' as well as ", as a matter of course.  Can't
        # hurt.
        $html = htmlspecialchars($html, ENT_QUOTES);
        return $html;
    }

    /**
     * Regex replace callback for armoring links against further processing.
     * @param $matches Array
     * @return string
     */
    private static function armorLinksCallback($matches)
    {
        return str_replace(':', '&#58;', $matches[1]);
    }

    /**
     * Return an associative array of attribute names and values from
     * a partial tag string. Attribute names are forces to lowercase,
     * character references are decoded to UTF-8 text.
     *
     * @param $text String
     * @return array
     */
    public static function decodeTagAttributes($text)
    {
        if (trim($text) == '') {
            return [];
        }

        // Revert all quotes to normal view in order to parse the attributes correctly
        $text = str_replace('&quot;', '"', $text);

        $attribs = [];
        $pairs = [];
        if (! preg_match_all(
            self::getAttribsRegex(),
            $text,
            $pairs,
            PREG_SET_ORDER
        )) {
            return $attribs;
        }

        foreach ($pairs as $set) {
            $attribute = strtolower($set[1]);
            $value = Sanitizer::getTagAttributeCallback($set);

            // Normalize whitespace
            $value = preg_replace('/[\t\r\n ]+/', ' ', $value);
            $value = trim($value);

            // Decode character references
            $attribs[$attribute] = Sanitizer::decodeCharReferences($value);
        }
        return $attribs;
    }

    /**
     * Pick the appropriate attribute value from a match set from the
     * attribs regex matches.
     *
     * @param $set Array
     * @throws MWException
     * @return string
     */
    private static function getTagAttributeCallback($set)
    {
        if (isset($set[6])) {
            # Illegal #XXXXXX color with no quotes.
            return $set[6];
        } elseif (isset($set[5])) {
            # No quotes.
            return $set[5];
        } elseif (isset($set[4])) {
            # Single-quoted
            return $set[4];
        } elseif (isset($set[3])) {
            # Double-quoted
            return $set[3];
        } elseif (! isset($set[2])) {
            # In XHTML, attributes must have a value.
            # For 'reduced' form, return explicitly the attribute name here.
            return $set[1];
        } else {
            throw new MWException("Tag conditions not met. This should never happen and is a bug.");
        }
    }

    /**
     * Normalize whitespace and character references in an XML source-
     * encoded text for an attribute value.
     *
     * See http://www.w3.org/TR/REC-xml/#AVNormalize for background,
     * but note that we're not returning the value, but are returning
     * XML source fragments that will be slapped into output.
     *
     * @param $text String
     * @return string
     */
    private static function normalizeAttributeValue($text)
    {
        return str_replace(
            '"',
            '&quot;',
            self::normalizeWhitespace(
                Sanitizer::normalizeCharReferences($text)
            )
        );
    }

    /**
     * @param $text string
     * @return mixed
     */
    private static function normalizeWhitespace($text)
    {
        return preg_replace(
            '/\r\n|[\x20\x0d\x0a\x09]/',
            ' ',
            $text
        );
    }

    /**
     * Normalizes whitespace in a section name, such as might be returned
     * by Parser::stripSectionName(), for use in the id's that are used for
     * section links.
     *
     * @param $section String
     * @return string
     */
    public static function normalizeSectionNameWhitespace($section)
    {
        return trim(preg_replace('/[ _]+/', ' ', $section));
    }

    /**
     * Ensure that any entities and character references are legal
     * for XML and XHTML specifically. Any stray bits will be
     * &amp;-escaped to result in a valid text fragment.
     *
     * a. named char refs can only be &lt; &gt; &amp; &quot;, others are
     *   numericized (this way we're well-formed even without a DTD)
     * b. any numeric char refs must be legal chars, not invalid or forbidden
     * c. use lower cased "&#x", not "&#X"
     * d. fix or reject non-valid attributes
     *
     * @param $text String
     * @return string
     * @private
     */
    public static function normalizeCharReferences($text)
    {
        return preg_replace_callback(
            self::CHAR_REFS_REGEX,
            ['Sanitizer', 'normalizeCharReferencesCallback'],
            $text
        );
    }

    /**
     * @param $matches String
     * @return string
     */
    public static function normalizeCharReferencesCallback($matches)
    {
        $ret = null;
        if ($matches[1] != '') {
            $ret = Sanitizer::normalizeEntity($matches[1]);
        } elseif ($matches[2] != '') {
            $ret = Sanitizer::decCharReference($matches[2]);
        } elseif ($matches[3] != '') {
            $ret = Sanitizer::hexCharReference($matches[3]);
        }
        if (is_null($ret)) {
            return htmlspecialchars($matches[0]);
        } else {
            return $ret;
        }
    }

    /**
     * If the named entity is defined in the HTML 4.0/XHTML 1.0 DTD,
     * return the equivalent numeric entity reference (except for the core &lt;
     * &gt; &amp; &quot;). If the entity is a MediaWiki-specific alias, returns
     * the HTML equivalent. Otherwise, returns HTML-escaped text of
     * pseudo-entity source (eg &amp;foo;)
     *
     * @param $name String
     * @return string
     */
    public static function normalizeEntity($name)
    {
        if (isset(self::$htmlEntityAliases[$name])) {
            return '&' . self::$htmlEntityAliases[$name] . ';';
        } elseif (in_array(
            $name,
            ['lt', 'gt', 'amp', 'quot']
        )) {
            return "&$name;";
        } elseif (isset(self::$htmlEntities[$name])) {
            return '&#' . self::$htmlEntities[$name] . ';';
        } else {
            return "&amp;$name;";
        }
    }

    /**
     * @return null|string
     */
    public static function decCharReference($codepoint)
    {
        $point = intval($codepoint);
        if (Sanitizer::validateCodepoint($point)) {
            return sprintf('&#%d;', $point);
        } else {
            return null;
        }
    }

    /**
     * @return null|string
     */
    public static function hexCharReference($codepoint)
    {
        $point = hexdec($codepoint);
        if (Sanitizer::validateCodepoint($point)) {
            return sprintf('&#x%x;', $point);
        } else {
            return null;
        }
    }

    /**
     * Returns true if a given Unicode codepoint is a valid character in XML.
     * @param $codepoint Integer
     * @return boolean
     */
    private static function validateCodepoint($codepoint)
    {
        return ($codepoint == 0x09)
            || ($codepoint == 0x0a)
            || ($codepoint == 0x0d)
            || ($codepoint >= 0x20 && $codepoint <= 0xd7ff)
            || ($codepoint >= 0xe000 && $codepoint <= 0xfffd)
            || ($codepoint >= 0x10000 && $codepoint <= 0x10ffff);
    }

    /**
     * Decode any character references, numeric or named entities,
     * in the text and return a UTF-8 string.
     *
     * @param $text String
     * @return string
     */
    public static function decodeCharReferences($text)
    {
        return preg_replace_callback(
            self::CHAR_REFS_REGEX,
            ['Sanitizer', 'decodeCharReferencesCallback'],
            $text
        );
    }

    /**
     * Decode any character references, numeric or named entities,
     * in the next and normalize the resulting string. (bug 14952)
     *
     * This is useful for page titles, not for text to be displayed,
     * MediaWiki allows HTML entities to escape normalization as a feature.
     *
     * @param string $text (already normalized, containing entities)
     * @return string (still normalized, without entities)
     */
    public static function decodeCharReferencesAndNormalize($text)
    {
        global $wgContLang;
        $text = preg_replace_callback(
            self::CHAR_REFS_REGEX,
            ['Sanitizer', 'decodeCharReferencesCallback'],
            $text, /* limit */
            -1,
            $count
        );

        if ($count) {
            return $wgContLang->normalize($text);
        } else {
            return $text;
        }
    }

    /**
     * @param $matches String
     * @return string
     */
    public static function decodeCharReferencesCallback($matches)
    {
        if ($matches[1] != '') {
            return Sanitizer::decodeEntity($matches[1]);
        } elseif ($matches[2] != '') {
            return Sanitizer::decodeChar(intval($matches[2]));
        } elseif ($matches[3] != '') {
            return Sanitizer::decodeChar(hexdec($matches[3]));
        }
        # Last case should be an ampersand by itself
        return $matches[0];
    }

    /**
     * Return UTF-8 string for a codepoint if that is a valid
     * character reference, otherwise U+FFFD REPLACEMENT CHARACTER.
     * @param $codepoint Integer
     * @return string
     * @private
     */
    public static function decodeChar($codepoint)
    {
        if (Sanitizer::validateCodepoint($codepoint)) {
            return codepointToUtf8($codepoint);
        } else {
            return UTF8_REPLACEMENT;
        }
    }

    /**
     * If the named entity is defined in the HTML 4.0/XHTML 1.0 DTD,
     * return the UTF-8 encoding of that character. Otherwise, returns
     * pseudo-entity source (eg "&foo;")
     *
     * @param $name String
     * @return string
     */
    public static function decodeEntity($name)
    {
        if (isset(self::$htmlEntityAliases[$name])) {
            $name = self::$htmlEntityAliases[$name];
        }
        if (isset(self::$htmlEntities[$name])) {
            return codepointToUtf8(self::$htmlEntities[$name]);
        } else {
            return "&$name;";
        }
    }

    /**
     * Fetch the whitelist of acceptable attributes for a given element name.
     *
     * @param $element String
     * @return array
     */
    public static function attributeWhitelist($element)
    {
        $list = Sanitizer::setupAttributeWhitelist();
        return isset($list[$element])
            ? $list[$element]
            : [];
    }

    /**
     * Foreach array key (an allowed HTML element), return an array
     * of allowed attributes
     * @return array
     */
    public static function setupAttributeWhitelist()
    {
        global $wgAllowRdfaAttributes, $wgHtml5, $wgAllowMicrodataAttributes;

        static $whitelist, $staticInitialised;
        $globalContext = implode('-', compact('wgAllowRdfaAttributes', 'wgHtml5', 'wgAllowMicrodataAttributes'));

        if (isset($whitelist) && $staticInitialised == $globalContext) {
            return $whitelist;
        }

        $common = [
            # HTML
            'id',
            'class',
            'style',
            'lang',
            'dir',
            'title',

            # WAI-ARIA
            'role',
        ];

        if ($wgAllowRdfaAttributes) {
            #RDFa attributes as specified in section 9 of http://www.w3.org/TR/2008/REC-rdfa-syntax-20081014
            $common = array_merge($common, [
                'about', 'property', 'resource', 'datatype', 'typeof',
            ]);
        }

        if ($wgHtml5 && $wgAllowMicrodataAttributes) {
            # add HTML5 microdata tags as specified by http://www.whatwg.org/specs/web-apps/current-work/multipage/microdata.html#the-microdata-model
            $common = array_merge($common, [
                'itemid', 'itemprop', 'itemref', 'itemscope', 'itemtype',
            ]);
        }

        $block = array_merge($common, ['align']);
        $tablealign = ['align', 'char', 'charoff', 'valign'];
        $tablecell = ['abbr',
            'axis',
            'headers',
            'scope',
            'rowspan',
            'colspan',
            'nowrap', # deprecated
            'width',  # deprecated
            'height', # deprecated
            'bgcolor', # deprecated
        ];

        # Numbers refer to sections in HTML 4.01 standard describing the element.
        # See: http://www.w3.org/TR/html4/
        $whitelist = [
            # 7.5.4
            'div' => $block,
            'center' => $common, # deprecated
            'span' => $block, # ??

            # 7.5.5
            'h1' => $block,
            'h2' => $block,
            'h3' => $block,
            'h4' => $block,
            'h5' => $block,
            'h6' => $block,

            # 7.5.6
            # address

            # 8.2.4
            # bdo

            # 9.2.1
            'em' => $common,
            'strong' => $common,
            'cite' => $common,
            'dfn' => $common,
            'code' => $common,
            'samp' => $common,
            'kbd' => $common,
            'var' => $common,
            'abbr' => $common,
            # acronym

            # 9.2.2
            'blockquote' => array_merge($common, ['cite']),
            # q

            # 9.2.3
            'sub' => $common,
            'sup' => $common,

            # 9.3.1
            'p' => $block,

            # 9.3.2
            'br' => ['id', 'class', 'title', 'style', 'clear'],

            # 9.3.4
            'pre' => array_merge($common, ['width']),

            # 9.4
            'ins' => array_merge($common, ['cite', 'datetime']),
            'del' => array_merge($common, ['cite', 'datetime']),

            # 10.2
            'ul' => array_merge($common, ['type']),
            'ol' => array_merge($common, ['type', 'start']),
            'li' => array_merge($common, ['type', 'value']),

            # 10.3
            'dl' => $common,
            'dd' => $common,
            'dt' => $common,

            # 11.2.1
            'table' => array_merge(
                $common,
                ['summary', 'width', 'border', 'frame',
                    'rules', 'cellspacing', 'cellpadding',
                    'align', 'bgcolor',
                ]
            ),

            # 11.2.2
            'caption' => array_merge($common, ['align']),

            # 11.2.3
            'thead' => array_merge($common, $tablealign),
            'tfoot' => array_merge($common, $tablealign),
            'tbody' => array_merge($common, $tablealign),

            # 11.2.4
            'colgroup' => array_merge($common, ['span', 'width'], $tablealign),
            'col' => array_merge($common, ['span', 'width'], $tablealign),

            # 11.2.5
            'tr' => array_merge($common, ['bgcolor'], $tablealign),

            # 11.2.6
            'td' => array_merge($common, $tablecell, $tablealign),
            'th' => array_merge($common, $tablecell, $tablealign),

            # 12.2 # NOTE: <a> is not allowed directly, but the attrib whitelist is used from the Parser object
            'a' => array_merge($common, ['href', 'rel', 'rev']), # rel/rev esp. for RDFa

            # 13.2
            # Not usually allowed, but may be used for extension-style hooks
            # such as <math> when it is rasterized, or if $wgAllowImageTag is
            # true
            'img' => array_merge($common, ['alt', 'src', 'width', 'height']),

            # 15.2.1
            'tt' => $common,
            'b' => $common,
            'i' => $common,
            'big' => $common,
            'small' => $common,
            'strike' => $common,
            's' => $common,
            'u' => $common,

            # 15.2.2
            'font' => array_merge($common, ['size', 'color', 'face']),
            # basefont

            # 15.3
            'hr' => array_merge($common, ['noshade', 'size', 'width']),

            # XHTML Ruby annotation text module, simple ruby only.
            # http://www.w3c.org/TR/ruby/
            'ruby' => $common,
            # rbc
            # rtc
            'rb' => $common,
            'rt' => $common, #array_merge( $common, array( 'rbspan' ) ),
            'rp' => $common,

            # MathML root element, where used for extensions
            # 'title' may not be 100% valid here; it's XHTML
            # http://www.w3.org/TR/REC-MathML/
            'math' => ['class', 'style', 'id', 'title'],

            # HTML 5 section 4.6
            'bdi' => $common,

        ];

        if ($wgHtml5) {
            # HTML5 elements, defined by:
            # http://www.whatwg.org/specs/web-apps/current-work/multipage/
            $whitelist += [
                'data' => array_merge($common, ['value']),
                'time' => array_merge($common, ['datetime']),
                'mark' => $common,

                // meta and link are only permitted by removeHTMLtags when Microdata
                // is enabled so we don't bother adding a conditional to hide these
                // Also meta and link are only valid in WikiText as Microdata elements
                // (ie: validateTag rejects tags missing the attributes needed for Microdata)
                // So we don't bother including $common attributes that have no purpose.
                'meta' => ['itemprop', 'content'],
                'link' => ['itemprop', 'href'],
            ];
        }

        $staticInitialised = $globalContext;

        return $whitelist;
    }

    /**
     * Take a fragment of (potentially invalid) HTML and return
     * a version with any tags removed, encoded as plain text.
     *
     * Warning: this return value must be further escaped for literal
     * inclusion in HTML output as of 1.10!
     *
     * @param string $text HTML fragment
     * @return string
     */
    public static function stripAllTags($text)
    {
        # Actual <tags>
        $text = StringUtils::delimiterReplace('<', '>', '', $text);

        # Normalize &entities and whitespace
        $text = self::decodeCharReferences($text);
        $text = self::normalizeWhitespace($text);

        return $text;
    }

    /**
     * Hack up a private DOCTYPE with HTML's standard entity declarations.
     * PHP 4 seemed to know these if you gave it an HTML doctype, but
     * PHP 5.1 doesn't.
     *
     * Use for passing XHTML fragments to PHP's XML parsing functions
     *
     * @return string
     */
    public static function hackDocType()
    {
        $out = "<!DOCTYPE html [\n";
        foreach (self::$htmlEntities as $entity => $codepoint) {
            $out .= "<!ENTITY $entity \"&#$codepoint;\">";
        }
        $out .= "]>\n";
        return $out;
    }

    /**
     * @param $url string
     * @return mixed|string
     */
    public static function cleanUrl($url)
    {
        # Normalize any HTML entities in input. They will be
        # re-escaped by makeExternalLink().
        $url = Sanitizer::decodeCharReferences($url);

        # Escape any control characters introduced by the above step
        $url = preg_replace_callback(
            '/[\][<>"\\x00-\\x20\\x7F\|]/',
            [__CLASS__, 'cleanUrlCallback'],
            $url
        );

        # Validate hostname portion
        $matches = [];
        if (preg_match('!^([^:]+:)(//[^/]+)?(.*)$!iD', $url, $matches)) {
            list( /* $whole */, $protocol, $host, $rest) = $matches;

            // Characters that will be ignored in IDNs.
            // http://tools.ietf.org/html/3454#section-3.1
            // Strip them before further processing so blacklists and such work.
            $strip = "/
				\\s|          # general whitespace
				\xc2\xad|     # 00ad SOFT HYPHEN
				\xe1\xa0\x86| # 1806 MONGOLIAN TODO SOFT HYPHEN
				\xe2\x80\x8b| # 200b ZERO WIDTH SPACE
				\xe2\x81\xa0| # 2060 WORD JOINER
				\xef\xbb\xbf| # feff ZERO WIDTH NO-BREAK SPACE
				\xcd\x8f|     # 034f COMBINING GRAPHEME JOINER
				\xe1\xa0\x8b| # 180b MONGOLIAN FREE VARIATION SELECTOR ONE
				\xe1\xa0\x8c| # 180c MONGOLIAN FREE VARIATION SELECTOR TWO
				\xe1\xa0\x8d| # 180d MONGOLIAN FREE VARIATION SELECTOR THREE
				\xe2\x80\x8c| # 200c ZERO WIDTH NON-JOINER
				\xe2\x80\x8d| # 200d ZERO WIDTH JOINER
				[\xef\xb8\x80-\xef\xb8\x8f] # fe00-fe0f VARIATION SELECTOR-1-16
				/xuD";

            $host = preg_replace($strip, '', $host);

            // @todo FIXME: Validate hostnames here

            return $protocol . $host . $rest;
        } else {
            return $url;
        }
    }

    /**
     * @param $matches array
     * @return string
     */
    public static function cleanUrlCallback($matches)
    {
        return urlencode($matches[0]);
    }

    /**
     * Does a string look like an e-mail address?
     *
     * This validates an email address using an HTML5 specification found at:
     * http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#valid-e-mail-address
     * Which as of 2011-01-24 says:
     *
     *   A valid e-mail address is a string that matches the ABNF production
     *   1*( atext / "." ) "@" ldh-str *( "." ldh-str ) where atext is defined
     *   in RFC 5322 section 3.2.3, and ldh-str is defined in RFC 1034 section
     *   3.5.
     *
     * This function is an implementation of the specification as requested in
     * bug 22449.
     *
     * Client-side forms will use the same standard validation rules via JS or
     * HTML 5 validation; additional restrictions can be enforced server-side
     * by extensions via the 'isValidEmailAddr' hook.
     *
     * Note that this validation doesn't 100% match RFC 2822, but is believed
     * to be liberal enough for wide use. Some invalid addresses will still
     * pass validation here.
     *
     * @since 1.18
     *
     * @param string $addr E-mail address
     * @return bool
     */
    public static function validateEmail($addr)
    {
        $result = null;
        if (! wfRunHooks('isValidEmailAddr', [$addr, &$result])) {
            return $result;
        }

        // Please note strings below are enclosed in brackets [], this make the
        // hyphen "-" a range indicator. Hence it is double backslashed below.
        // See bug 26948
        $rfc5322_atext = "a-z0-9!#$%&'*+\\-\/=?^_`{|}~";
        $rfc1034_ldh_str = "a-z0-9\\-";

        $HTML5_email_regexp = "/
		^                      # start of string
		[$rfc5322_atext\\.]+    # user part which is liberal :p
		@                      # 'apostrophe'
		[$rfc1034_ldh_str]+       # First domain part
		(\\.[$rfc1034_ldh_str]+)*  # Following part prefixed with a dot
		$                      # End of string
		/ix"; // case Insensitive, eXtended

        return (bool) preg_match($HTML5_email_regexp, $addr);
    }
}
