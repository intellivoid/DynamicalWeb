<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb;

    class HtmlMarkup
    {
        /**
         * Specifies if attribute values and text input should be protected from XSS injection
         *
         * @var boolean
         */
        public static $avoidXSS = true;

        /**
         * The language convention used for XSS avoiding
         *
         * @var int
         */
        public static $outputLanguage = ENT_XML1;

        protected static $instance = null;
        protected $top = null;
        protected $parent = null;
        protected $tag = null;
        public $attributeList = null;
        protected $classList = null;
        protected $content = null;
        protected $text = '';
        protected $autoclosed = false;
        protected $autocloseTagsList = ['img', 'br', 'hr', 'input', 'area', 'link', 'meta', 'param', 'base', 'col', 'command', 'keygen', 'source'];

        /**
         * Constructor
         * @param mixed $tag
         * @param HtmlMarkup|null $top
         * @return static instance
         */
        protected function __construct($tag, HtmlMarkup $top = null)
        {
            $this->tag = $tag;
            $this->top =& $top;
            $this->attributeList = array();
            $this->classList = array();
            $this->content = array();
            $this->autoclosed = in_array($this->tag, $this->autocloseTagsList);
            $this->text = '';
            return $this;
        }

        /**
         * Builds markup from static context
         * @param string $tag The tag name
         * @param array $content The content of the current tag, first argument can be an array containing the attributes
         * @return static
         */
        public static function __callStatic(string $tag, array $content)
        {
            return self::createElement($tag)
                ->attr(count($content) && is_array($content[0]) ? array_pop($content) : array())
                ->text(implode('', $content));
        }

        /**
         * Add a children to the current element
         * @param string $tag The name of the tag
         * @param array $content The content of the current tag, first argument can be an array containing the attributes
         * @return HtmlMarkup instance
         */
        public function __call(string $tag, array $content)
        {
            return $this
                ->addElement($tag)
                ->attr(count($content) && is_array($content[0]) ? array_pop($content) : array())
                ->text(implode('', $content));
        }

        /**
         * Alias for getParent()
         * @return HtmlMarkup
         */
        public function __invoke(): ?HtmlMarkup
        {
            return $this->getParent();
        }

        /**
         * Create a new HtmlMarkup
         * @param string $tag
         * @return static instance
         * @noinspection PhpMissingReturnTypeInspection
         */
        public static function createElement(string $tag = '')
        {
            self::$instance = new static($tag);
            return self::$instance;
        }

        /**
         *
         * Add element at an existing HtmlMarkup
         * @param HtmlMarkup|string $tag
         * @return static instance
         */
        public function addElement($tag = ''): HtmlMarkup
        {
            /** @noinspection PhpConditionCheckedByNextConditionInspection */
            $htmlTag = (is_object($tag) && $tag instanceof self) ? clone $tag : new static($tag);
            $htmlTag->top = $this->getTop();
            $htmlTag->parent = &$this;

            $this->content[] = $htmlTag;
            return $htmlTag;
        }

        /**
         * (Re)Define an attribute or many attributes
         * @param string|array $attribute
         * @param string|null $value
         * @return static instance
         */
        public function set($attribute, string $value = null): HtmlMarkup
        {
            if (is_array($attribute)) {
                foreach ($attribute as $key => $value) {
                    $this[$key] = $value;
                }
            } else {
                $this[$attribute] = $value;
            }
            return $this;
        }

        /**
         * alias to method "set"
         * @param string|array $attribute
         * @param string|null $value
         * @return static instance
         */
        public function attr($attribute, string $value = null): HtmlMarkup
        {
            return call_user_func_array(array($this, 'set'), func_get_args());
        }

        /**
         * Checks if an attribute is set for this tag and not null
         *
         * @param string $attribute The attribute to test
         * @return boolean The result of the test
         */
        public function offsetExists(string $attribute): bool
        {
            return isset($this->attributeList[$attribute]);
        }

        /**
         * Returns the value the attribute set for this tag
         *
         * @param string $attribute The attribute to get
         * @return mixed The stored result in this object
         * @noinspection PhpUnused
         */
        public function offsetGet(string $attribute)
        {
            return $this->offsetExists($attribute) ? $this->attributeList[$attribute] : null;
        }

        /**
         * Sets the value an attribute for this tag
         *
         * @param string $attribute The attribute to set
         * @param mixed $value The value to set
         * @return void
         * @noinspection PhpUnused
         */
        public function offsetSet(string $attribute, $value)
        {
            $this->attributeList[$attribute] = $value;
        }

        /**
         * Removes an attribute
         *
         * @param mixed $attribute The attribute to unset
         * @return void
         * @noinspection PhpUnused
         */
        public function offsetUnset($attribute)
        {
            if ($this->offsetExists($attribute)) {
                unset($this->attributeList[$attribute]);
            }
        }

        /**
         *
         * Define text content
         * @param string $value
         * @return static instance
         */
        public function text(string $value): HtmlMarkup
        {
            /** @noinspection PhpRedundantOptionalArgumentInspection */
            $this->addElement('')->text = static::$avoidXSS ? static::unXSS($value) : $value;
            return $this;
        }

        /**
         * Returns the top element
         * @return static
         */
        public function getTop(): ?HtmlMarkup
        {
            return $this->top===null ? $this : $this->top;
        }

        /**
         *
         * Return parent of current element
         */
        public function getParent()
        {
            return $this->parent;
        }

        /**
         * Return first child of parent of current object
         * @noinspection PhpExpressionAlwaysNullInspection
         * @noinspection PhpUnused
         */
        public function getFirst()
        {
            return is_null($this->parent) ? null : $this->parent->content[0];
        }

        /**
         * Return previous element or itself
         *
         * @return static instance
         */
        public function getPrevious(): HtmlMarkup
        {
            $prev = $this;
            $find = false;
            if (!is_null($this->parent))
            {
                foreach ($this->parent->content as $c)
                {
                    if ($c === $this)
                    {
                        break;
                    }

                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    if (!$find)
                    {
                        $prev = $c;
                    }
                }
            }
            return $prev;
        }

        /**
         * @return static|null last child of parent of current object
         * @noinspection PhpUnused
         */
        public function getNext(): ?HtmlMarkup
        {
            $next = null;
            $find = false;
            if (!is_null($this->parent))
            {
                foreach ($this->parent->content as $c)
                {
                    if ($find)
                    {
                        $next = &$c;
                        break;
                    }

                    if ($c == $this)
                    {
                        $find = true;
                    }
                }
            }
            return $next;
        }

        /**
         * @return static|null last child of parent of current object
         * @noinspection PhpExpressionAlwaysNullInspection
         * @noinspection PhpUnused
         */
        public function getLast(): ?HtmlMarkup
        {
            return is_null($this->parent) ? null : $this->parent->content[count($this->parent->content) - 1];
        }

        /**
         * @return static|null return parent or null
         */
        public function remove(): ?HtmlMarkup
        {
            $parent = $this->parent;
            if (!is_null($parent))
            {
                foreach ($parent->content as $key => $value)
                {
                    if ($parent->content[$key] == $this)
                    {
                        unset($parent->content[$key]);
                        return $parent;
                    }
                }
            }
            return null;
        }

        /**
         * Generation method
         * @return string
         */
        public function __toString()
        {
            return $this->getTop()->toString();
        }

        /**
         * Prints out the HTML contents
         */
        public function print()
        {
            print($this->getTop()->toString());
        }

        /**
         * Generation method
         * @return string
         */
        public function toString(): string
        {
            $string = '';
            if (!empty($this->tag))
            {
                $string .=  '<' . $this->tag;
                $string .= $this->attributesToString();
                if ($this->autoclosed)
                {
                    $string .= '/>';
                }
                else
                {
                    $string .= '>' . $this->contentToString() . '</' . $this->tag . '>';
                }
            }
            else
            {
                $string .= $this->text;
                $string .= $this->contentToString();
            }

            return $string;
        }

        /**
         * return current list of attribute as a string $key="$val" $key2="$val2"
         * @return string
         */
        protected function attributesToString(): string
        {
            $string = '';
            $XMLConvention = in_array(static::$outputLanguage, array(ENT_XML1, ENT_XHTML));
            if (!empty($this->attributeList))
            {
                foreach ($this->attributeList as $key => $value)
                {
                    if ($value!==null && ($value!==false || $XMLConvention))
                    {
                        $string.= ' ' . $key;
                        if ($value===true)
                        {
                            if ($XMLConvention)
                            {
                                $value = $key;
                            }
                            else
                            {
                                continue;
                            }
                        }
                        $string.= '="' . implode(
                                ' ',
                                array_map(
                                    static::$avoidXSS ? 'static::unXSS' : 'strval',
                                    is_array($value) ? $value : array($value)
                                )
                            ) . '"';
                    }
                }
            }

            return $string;
        }

        /**
         * return current list of content as a string
         * @return string
         */
        protected function contentToString(): string
        {
            $string = '';
            if (!is_null($this->content))
            {
                foreach ($this->content as $c)
                {
                    $string .= $c->toString();
                }
            }

            return $string;
        }

        /**
         * Protects value from XSS injection by replacing some characters by XML / HTML entities
         * @param string $input The unprotected value
         * @return string A safe string
         */
        public static function unXSS(string $input): string
        {
            if (version_compare(phpversion(), '5.4', '<'))
            {
                return htmlspecialchars($input);
            }

            return htmlentities($input, ENT_QUOTES | ENT_DISALLOWED | static::$outputLanguage);
        }
    }