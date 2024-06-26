#!/usr/bin/env bash

# TODO rename this as appropriate or find a similar function and merge them.
function _resolve_file() {
  local path="$1"

  local directory
  local basename

  directory=${path%/*}
  basename=${path##*/}

  if [ -d "$directory" ]; then
    directory="$(cd "$directory"; pwd -P)"
  fi

  echo "$directory/$basename"
}
# TODO rename this as appropriate or find a similar function and merge them.
function _resolve_dir() {
  local directory="$1"

  if [ -d "$directory" ]; then
    directory="$(cd "$directory"; pwd -P)"
  fi

  echo "$directory"
}

function _cloudy_detect_installation_type() {
  local base
  local check_composer
  local check_cloudy
  base=$(dirname $SCRIPT)

  check_composer="$(_resolve_file "$base/../../../composer.json")"
  [ -f "$(_resolve_file "$check_composer")" ] && echo $CLOUDY_INSTALL_TYPE_COMPOSER && return 0

  check_composer="$(_resolve_file "$base/composer.json")"
  check_cloudy="$(_resolve_file "$base/cloudy/cloudy.sh")"
  [ -f "$check_composer" ] && [ -f "$check_cloudy" ]  && echo $CLOUDY_INSTALL_TYPE_CORE && return 0

  check_composer="$(_resolve_file "$base/framework/cloudy/composer.json")"
  check_cloudy="$(_resolve_file "$base/framework/cloudy/cloudy.sh")"
  [ -f "$check_composer" ] && [ -f "$check_cloudy" ]  && echo $CLOUDY_INSTALL_TYPE_SELF && return 0

  check_composer="$(_resolve_file "$base/../../cloudy/cloudy/composer.json")"
  check_cloudy="$(_resolve_file "$base/../../../cloudypm.lock")"
  [ -f "$check_composer" ] && [ -f "$check_cloudy" ] && echo $CLOUDY_INSTALL_TYPE_PM && return 0

  return 1
}

# Echo the detected app root by installation type.
#
# Returns 1 if detection failed.
function _cloudy_detect_app_root_by_installation() {
  local installation_type="$1"

  local base
  local app_root
  base="$(dirname "$SCRIPT")"
  case "$installation_type" in
  "$CLOUDY_INSTALL_TYPE_SELF")
    app_root="$base"
    ;;
  "$CLOUDY_INSTALL_TYPE_COMPOSER")
    app_root="$base/../../../"
    ;;
  "$CLOUDY_INSTALL_TYPE_CORE")
    app_root="$base"
    ;;
  "$CLOUDY_INSTALL_TYPE_PM")
    app_root="$base/../../../"
    ;;
  *)
    return 1
  esac
  echo "$(_resolve_file "$app_root")"
  return 0
}

function _cloudy_detect_composer_vendor_by_installation() {
    local installation_type="$1"

    local base
    local vendor
    base="$(dirname "$SCRIPT")"
    case "$installation_type" in
    "$CLOUDY_INSTALL_TYPE_SELF")
      vendor="$base/framework/cloudy/vendor"
      ;;
    "$CLOUDY_INSTALL_TYPE_COMPOSER")
      vendor="$base/../../../vendor"
      ;;
    "$CLOUDY_INSTALL_TYPE_CORE")
      # First assume the app has a vendor directory, which cloudy is going to leverage...
      vendor="$base/vendor"
      # ... if not then use the one inside cloudy core.
      [ ! -d "$vendor" ] && vendor="$base/cloudy/vendor"
      ;;
    "$CLOUDY_INSTALL_TYPE_PM")
      vendor="$base/../../cloudy/cloudy/vendor"
      ;;
    *)
      return 1
    esac
    echo "$(_resolve_file "$vendor")"
    return 0
}

#
# @file
# Non-public functions used by the cloudy API.
#
function _cloudy_define_cloudy_vars() {
  # todo Can we move more things here, checking scope is not lost.
  LI="├──"
  LIL="└──"
  LI2="│   $LI"
  LIL2="│   $LIL"

  CLOUDY_INSTALL_TYPE_SELF='self'
  CLOUDY_INSTALL_TYPE_COMPOSER='composer'
  CLOUDY_INSTALL_TYPE_CORE='cloudy_core'
  CLOUDY_INSTALL_TYPE_PM='cloudy_pm'
}

function _cloudy_bootstrap_php() {
  if [[ ! "$CLOUDY_PHP" ]]; then
    CLOUDY_PHP="$(command -v php)"
  fi
  [[ !  "$CLOUDY_PHP" ]] && fail_because "\$CLOUDY_PHP cannot be set; PHP not found." && return 1
  [[ ! -x "$CLOUDY_PHP" ]] && fail_because "\$CLOUDY_PHP ($CLOUDY_PHP) is not executable" && return 1
  local php_version=$("$CLOUDY_PHP" -v | head -1 | grep -E "PHP ([0-9.]+)")
  [[ ! "$php_version" ]] && fail_because "\$CLOUDY_PHP ($CLOUDY_PHP) does not appear to be a PHP binary; $CLOUDY_PHP -v failed to display PHP version" && return 1
  return 0
}

function _cloudy_bootstrap_translations() {
  # todo Document this and add to schema.
  eval $(get_config_as "lang" "language" "en")
  CLOUDY_LANGUAGE=$lang

  # todo may not need to do these two?
  CLOUDY_SUCCESS=$(translate "Completed successfully.")
  CLOUDY_FAILED=$(translate "Failed.")
}

function _cloudy_bootstrap() {
  SECONDS=0
  local aliases
  local value
  local options
  local command

  _cloudy_bootstrap_translations

  command=$(get_command)
  # Add in the alias options based on master options.
  for option in "${CLOUDY_OPTIONS[@]}"; do
    value="true"
    [[ "$option" =~ ^(.*)\=(.*) ]] && option=${BASH_REMATCH[1]} && value=${BASH_REMATCH[2]}
    eval $(get_config_as 'aliases' "commands.${command}.options.${option}.aliases")
    for alias in ${aliases[@]}; do
      if ! has_option $alias; then
        CLOUDY_OPTIONS=("${CLOUDY_OPTIONS[@]}" "$alias")
        eval "CLOUDY_OPTION__$(md5_string $alias)=\"$value\""
      fi
    done
  done

  # Using aliases search for the master option.
  eval $(get_config_keys_as 'options' "commands.${command}.options")

  for master_option in "${options[@]}"; do
    eval $(get_config_as -a 'aliases' "commands.${command}.options.${master_option}.aliases")
    for alias in "${aliases[@]}"; do
      if has_option $alias && ! has_option $master_option; then
        value=$(get_option "$alias")
        CLOUDY_OPTIONS=("${CLOUDY_OPTIONS[@]}" "$master_option")
        eval "CLOUDY_OPTION__$(md5_string $master_option)=\"$value\""
      fi
    done
  done
}

##
# Delete $CACHED_CONFIG_FILEPATH as necessary.
#
function _cloudy_auto_purge_config() {
  local purge=false

  # Log the reason for the purge.
  if [[ "$cloudy_development_do_not_cache_config" == true ]]; then
    purge=true
    write_log_dev_warning "Configuration purge detected due to \$cloudy_development_do_not_cache_config = true."
  elif _cloudy_has_config_changed; then
    purge=true
    write_log_notice "Config changes detected in \"$(basename $_cloudy_has_config_changed__file)\"."
  else
    local cache_id
    if [[ -f "$CACHED_CONFIG_HASH_FILEPATH" ]]; then
      cache_id=$(cat "$CACHED_CONFIG_HASH_FILEPATH")
    fi
    if [[ "$cache_id" != "$config_cache_id" ]]; then
      purge=true
      write_log_notice "Config hash change detected in \"$(basename $CACHED_CONFIG_HASH_FILEPATH)\"."
    fi
  fi

  if [[ "$purge" == true ]]; then
    if ! rm -f "$CACHED_CONFIG_FILEPATH"; then
      fail_because "Could not rm $CACHED_CONFIG_FILEPATH during purge."
      write_log_critical "Cannot delete $CACHED_CONFIG_FILEPATH.  Cached configuration may be stale."
    fi
    if ! rm -f "$CACHED_CONFIG_JSON_FILEPATH"; then
      fail_because "Could not rm $CACHED_CONFIG_JSON_FILEPATH during purge."
      write_log_critical "Cannot delete $CACHED_CONFIG_JSON_FILEPATH.  Cached configuration may be stale."
    fi
  fi

  has_failed && exit_with_failure "Cannot auto purge config."
  return 0
}

##
# Detect if cached config is stale against $CONFIG.
#
function _cloudy_has_config_changed() {
  # When configuration gets cached, this file gets created with a line for
  # every config file that was used to generate the cached config.  Each line
  # is the absolute filepath and the modified timestamp of that file.  We will
  # compare the timestamp of the cached version against the actual version
  # here and thus know if the configuration changed and needs to be recached.
  [[ -f "$CACHED_CONFIG_MTIME_FILEPATH" ]] || touch "$CACHED_CONFIG_MTIME_FILEPATH" || fail
  while read path cached_mtime; do

    # If we discover a newer file in this step, then the config has changed.
    [[ $(_cloudy_get_file_mtime $path) -gt "$cached_mtime" ]] && _cloudy_has_config_changed__file="$path" && return 0
  done < "$CACHED_CONFIG_MTIME_FILEPATH"
  return 1
}

function _cloudy_get_file_mtime() {
  local filepath=$1
  [[ -e $filepath ]] && echo $("$CLOUDY_PHP" -r "echo filemtime('$filepath');")
}

##
# Return config eval code for a given config path.
#
# @param string
#   The config path, e.g. "commands.help.help"
# @param string
#   The default value if not found. This yields an exit status of 2.
#
# Options:
# --as={custom_var_name}
# --keys You want to return array keys.
# --mutator={function name} Optional mutator function name.
#
function _cloudy_get_config() {
  local config_path="$1"
  local default_value="$2"

  local default_type
  local var_name
  local var_type
  local var_value
  local var_code
  local array_keys
  local mutator
  local eval_code
  local dev_null
  local code
  local cached_var_name
  local cached_var_name_keys
  local file_list
  local config_path_base=${cloudy_config_22b41169ff3731365de5e8293e01c831}

  # Determine if we have an absolute relative path base or, if not prepend $ROOT.
  [[ "${config_path_base:0:1}" != '/' ]] && config_path_base="${ROOT}/$config_path_base"

  # Remove trailing / for proper path construction.
  config_path_base=${config_path_base%/}

  parse_args "$@"
  config_path=${parse_args__args[0]}

  # We have to use a hash or sometimes the name created will not work for a
  # BASH variable.
  local config_path_hash=$(md5_string $config_path)
  cached_var_name="cloudy_config_${config_path_hash}"
  cached_var_name_keys="cloudy_config_keys_${config_path_hash}"

  # The --keys option has been used
  get_array_keys=${parse_args__options__keys}
  [[ "$get_array_keys" ]] && cached_var_name="$cached_var_name_keys"
  default_value=${parse_args__args[1]}

  # Use the synonym if --as is passed
  var_name=${parse_args__options__as:-${config_path//./_}}

  [[ "${parse_args__options__a}" == true ]] && default_type='array'
  mutator=${parse_args__options__mutator}

  var_value=$(eval "echo "\$$cached_var_name"")

  if [[ "$var_value" ]]; then
    code=$(declare -p "$cached_var_name")
    code="${code/$cached_var_name=/var_value=}"
    eval "$code"
  fi

  # Todo should we try and autodetect?
  var_type="$default_type"

  # Determine the default value
  # @todo How to handle array defaults, syntax?
  # @link https://trello.com/c/6JXskrQn/9-c-619-allow-arrays-to-have-default-values-in-getconfig
  if ! [[ "$var_value" ]]; then
    if [[ "$var_type" == 'array' ]]; then
      # Todo this is not fully implemented yet.
      local eval_code="declare -a local $cached_var_name=("$default_value")"
    else
      local eval_code="local $cached_var_name="$default_value""
    fi
    eval $eval_code 2>/dev/null
  fi

  # Determine what type of array.
  if [[ "$var_type" == "array" ]]; then
    eval "local var_keys=("\${$cached_var_name_keys[@]}")"
    if [[ "${var_keys[0]}" == 0 ]] || [[ -z "${var_keys[0]}" ]]; then
      var_type="indexed_array"
    else
      var_type="associative_array"
    fi
  fi

  # It's an array and the keys are being asked for.
  if [[ "$get_array_keys" ]] && [[ "$var_type" =~ _array$ ]]; then
    #todo mutator for array values.
    code=$(declare -p $cached_var_name_keys)
    code="${code//$cached_var_name=/$var_name=}"

  elif [[ "$var_type" == "associative_array" ]]; then
    code=''
    for key in "${var_keys[@]}"; do

      cached_var_name=cloudy_config_$(md5_string ${config_path}.${key})

      if [[ "$mutator" == "_cloudy_realpath" ]]; then
        local path=$(eval "echo \$$cached_var_name")

        # Replace ~ with the actual home page
        path=$(echo ${path/\~/"$HOME"})

        # On first pass we will try to expand globbed filenames, which will
        # cause file_list to be longer than var_value.
        file_list=()

        # Make relative to $ROOT.
        [[ "$path" ]] && [[ "$path" != null ]] && [[ "${path:0:1}" != "/" ]] && path=${config_path_base}/${path}

        # This will expand a glob finder.
        if [ -d "$path" ]; then
          file_list=("${file_list[@]}" $path)
        elif [ -f "$path" ]; then
          file_list=("${file_list[@]}" $(ls $path))
        elif [[ "$path" != null ]]; then
          file_list=("${file_list[@]}" $path)
        fi

        # Glob may have increased our file_list so we apply realpath to all
        # of them here.
        local i=0
        for path in "${file_list[@]}"; do
          if [ -e "$path" ]; then
            file_list[$i]=$(realpath "$path")
          fi
          let i++
        done
        if [[ ${#file_list[@]} -eq 1 ]]; then
          eval "$cached_var_name="${file_list[0]}""
        else
          eval "$cached_var_name=("${file_list[@]}")"
        fi
      fi

      var_code=$(declare -p $cached_var_name)
      code="${code}${var_code/$cached_var_name/${var_name}_${key}};"
    done
  else
    if [[ "$mutator" == "_cloudy_realpath" ]]; then

      # On first pass we will try to expand globbed filenames, which will
      # cause file_list to be longer than var_value.
      file_list=()
      for path in "${var_value[@]}"; do

        # Replace ~ with the actual home page
        path=$(echo ${path/\~/"$HOME"})

        # Replace tokens
        path=$(echo ${path/\{APP_ROOT\}/"$APP_ROOT"})

        # Make relative to $ROOT.
        [[ "$var_value" ]] && [[ "$var_value" != null ]] && [[ "${path:0:1}" != "/" ]] && path=${config_path_base}/${path}

        # This will expand a glob finder.
        if [ -d "$path" ]; then
          file_list=("${file_list[@]}" $path)
        elif [ -f "$path" ]; then
          file_list=("${file_list[@]}" $(ls $path))
        elif [[ "$path" != null ]]; then
          file_list=("${file_list[@]}" $path)
        fi
      done

      # Glob may have increased our file_list so we apply realpath to all
      # of them here.
      local i=0
      for path in "${file_list[@]}"; do
        if [ -e "$path" ]; then
          file_list[$i]=$(realpath "$path")
        fi
        let i++
      done
      eval "$cached_var_name=("${file_list[@]}")"
    fi
    code=$(declare -p $cached_var_name)
    code="${code//$cached_var_name=/$var_name=}"
  fi

  echo ${code%;} && return 0
}

function _cloudy_exit() {
  event_dispatch "exit" $CLOUDY_EXIT_STATUS
  [[ "$CLOUDY_EXIT_STATUS" -eq 0 ]] && write_log_info "Exit status is: $CLOUDY_EXIT_STATUS"
  [[ "$CLOUDY_EXIT_STATUS" -ne 0 ]] && write_log_notice "Exit status is: $CLOUDY_EXIT_STATUS"
  exit $CLOUDY_EXIT_STATUS
}

##
# Prepare a message with optional suffix and fallback.
#
# * default is used if no override is given.
# * Ensures ends with period.
#
function _cloudy_message() {
  local override=$1
  local default=$2
  local suffix=$3

  if [[ "$override" ]]; then
    echo ${override%.}${suffix} && return 0
  fi
  echo ${default%.}${suffix} && return 2
}

# Echo using color
#
# $1 - The ANSI color value, e.g. 30-37, 39
# $2 - The message to echo.
# $3 - The intensity 0 dark, 1 light. Defaults to 1.
# $4 - The background color value. 40-47, 49
#
# @link https://misc.flogisoft.com/bash/tip_colors_and_formatting
#
# Returns 0 if .
function _cloudy_echo_color() {
  local color=$1
  local message="$2"
  local intensity=${3:-1}
  local bg=$4

  # tput is more portable so we use that and convert to it's colors.
  # https://linux.101hacks.com/ps1-examples/prompt-color-using-tput/
  let color-=30
  [[ $intensity -eq 1 ]] && echo -n $(tty -s && tput bold)
  if [[ "$bg" ]]; then
    let bg-=40
    echo -n $(tty -s && tput setab $bg)
  fi
  echo -n $(tty -s && tput setaf $color)
  echo -n "${message}"
  echo -n $(tty -s && tput sgr0)
  echo
}

# Echo a demonstration of the ANSI color rainbow.
#
# Returns nothing.
function _cloudy_echo_ansi_rainbow() {
  for ((i = 30; i < 38; i++)); do echo -e "\033[0;"$i"m Normal: (0;$i); \033[1;"$i"m Light: (1;$i)"; done
}

function _cloudy_echo_tput_rainbow() {
  for c in {0..255}; do
    tty -s && tput setaf $c
    tty -s && tput setaf $c | cat -v
    echo =$c
  done
}

function _cloudy_echo_credits() {
  echo "Cloudy $(get_version) by Aaron Klump"
}

##
# Echo a list of items with bullets in color
#
function _cloudy_echo_list() {
  parse_args "$@"

  local line_item
  local items_color=$1
  local bullets_color=$2
  local intensity=${parse_args__options__i:-1}
  local bullet
  local item

  for i in "${echo_list__array[@]}"; do
    bullet="$LI"
    if [[ "$bullets_color" ]]; then
      bullet=$(_cloudy_echo_color $bullets_color "$LI")
    fi
    item="$line_item"
    if [[ "$items_color" ]]; then
      item=$(_cloudy_echo_color $items_color "$line_item")
    fi
    [[ "$line_item" ]] && echo "$bullet $item"
    line_item="$i"
  done

  bullet="$LIL"
  if [[ "$bullets_color" ]]; then
    bullet=$(_cloudy_echo_color $bullets_color "$LIL")
  fi
  item="$line_item"
  if [[ "$items_color" ]]; then
    item=$(_cloudy_echo_color $items_color "$line_item")
  fi
  [[ "$line_item" ]] && echo "$bullet $item"
}

function _cloudy_exit_with_success() {
  local message=$1
  echo && echo_blue "👍  $message"

  ## Write out the failure messages if any.
  if [ ${#CLOUDY_SUCCESSES[@]} -gt 0 ]; then
    echo_list__array=("${CLOUDY_SUCCESSES[@]}")
    echo_blue_list
  fi

  echo
  CLOUDY_EXIT_STATUS=0 && _cloudy_exit
}

##
# Echo command or command alias' target.
#
function _cloudy_get_master_command() {
  local command_or_alias="$1"

  # See if it's a master command.
  eval $(get_config_keys "commands")
  array_has_value__array=(${commands[@]})
  array_has_value "$command_or_alias" && echo $command_or_alias && return 0

  # Look for command as an alias.
  for c in "${commands[@]}"; do
    eval $(get_config_as -a "aliases" "commands.$c.aliases")
    array_has_value__array=(${aliases[@]})
    array_has_value "$command_or_alias" && echo $c && return 0
  done

  return 1
}

##
# Set $_cloudy_get_valid_operations_by_command__array to all defined operations included aliases for a given op.
#
function _cloudy_get_valid_operations_by_command() {
  local command=$1

  local option
  local options
  local aliases

  eval $(get_config_keys_as 'options' -a "commands.${command}.options")

  for option in "${options[@]}"; do
    eval $(get_config_as 'aliases' -a "commands.${command}.options.${option}.aliases")

    options=("${options[@]}" "${aliases[@]}")
  done

  _cloudy_get_valid_operations_by_command__array=("${options[@]}")
}

function _cloudy_help_commands() {
  local commands
  local help_command
  local help

  echo_title "$(get_title) VER $(get_version)"

  echo_heading "Available commands:"
  echo

  eval $(get_config_keys "commands")
  for help_command in "${commands[@]}"; do
    eval $(get_config_as 'help' "commands.$help_command.help")
    echo_list__array=("${echo_list__array[@]}" "$(echo_green "${help_command}") $help")
  done
  echo_list
}

function _cloudy_help_for_single_command() {
  local command_help_topic=$1

  local arguments
  local options
  local usage
  local option
  local option_value
  local option_type
  local help_option
  local help_options
  local help_alias
  local help_argument

  eval $(get_config_keys_as 'arguments' -a "commands.${command_help_topic}.arguments")
  eval $(get_config_keys_as -a 'options' "commands.${command_help_topic}.options")

  echo_title "Help Topic: $command_help_topic"

  eval $(get_config_as 'help' "commands.${command_help_topic}.help")
  echo_green "$help"
  echo

  usage="./$(basename $SCRIPT) CMD"
  [ ${#arguments} -gt 0 ] && usage="$usage <arguments>"
  [ ${#options} -gt 0 ] && usage="$usage <options>"

  # Generate a list of command + aliases.
  eval $(get_config_as 'usage_commands' "commands.${command_help_topic}.aliases")
  usage_commands=("$command_help_topic" ${usage_commands[@]})
  for usage_command in "${usage_commands[@]}"; do
    echo_list__array=("${echo_list__array[@]}" "${usage/CMD/$usage_command}")
  done
  echo_yellow "Usage:"
  echo_list
  echo

  # The arguments.
  if [ ${#arguments} -gt 0 ]; then
    echo_yellow "Arguments:"
    echo_list__array=()

    for help_argument in "${arguments[@]}"; do
      eval $(get_config_as 'help' "commands.${command_help_topic}.arguments.${help_argument}.help")
      echo_list__array=("${echo_list__array[@]}" "$(echo_green "<$help_argument>") $help")
    done
    echo_list
    echo
  fi

  # The options.
  if [ ${#options} -gt 0 ]; then
    echo_yellow "Available options:"
    echo_list__array=()

    for option in "${options[@]}"; do

      option_value=''
      eval $(get_config_as 'option_type' "commands.${command_help_topic}.options.${option}.type" "boolean")
      [[ "$option_type" != "boolean" ]] && option_value="=<$option_type>"

      help_options=("$option")

      # Add in the aliases
      eval $(get_config_as -a 'aliases' "commands.${command_help_topic}.options.${option}.aliases")
      for help_alias in "${aliases[@]}"; do
        help_options=("${help_options[@]}" "$help_alias")
      done

      array_sort_by_item_length__array=(${help_options[@]})
      array_sort_by_item_length

      # Add in hyphens and values
      help_options=()
      for help_option in "${array_sort_by_item_length__array[@]}"; do
        if [ ${#help_option} -eq 1 ]; then
          help_options=("${help_options[@]}" "-${help_option}${option_value}")
        else
          help_options=("${help_options[@]}" "--${help_option}${option_value}")
        fi
      done

      array_join__array=(${help_options[@]})
      help_options=$(array_join ", ")

      eval $(get_config_as 'help' "commands.${command_help_topic}.options.${option}.help")

      echo_list__array=("${echo_list__array[@]}" "$(echo_green "$help_options") $help")
    done
    echo_list
  fi
}

function _cloudy_debug_helper() {
  local sidebar=''
  local IFS=";"
  local default
  local fg
  local bg
  local message
  local basename
  local funcname
  local lineno
  read default fg bg message basename funcname lineno <<<"$@"
  [[ "$basename" ]] && sidebar="$sidebar${basename##./}"
  [[ "$funcname" ]] && sidebar="$funcname in $sidebar"
  [[ "$lineno" ]] && sidebar="$sidebar on line $lineno"
  [[ "$sidebar" ]] || sidebar="$default"
  echo && echo "$(tty -s && tput setaf $fg)$(tty -s && tput setab $bg) $sidebar $(tty -s && tput smso) "$message" $(tty -s && tput sgr0)" && echo
}

function _cloudy_write_log() {
  [[ "$LOGFILE" ]] || return
  local level="$1"
  shift
  local directory=$(dirname $LOGFILE)
  test -d "$directory" || mkdir -p "$directory"
  touch "$LOGFILE"
  echo "[$(date)] [$level] $@" >>"$LOGFILE"
}

##
# @see event_dispatch
#
function _cloudy_trigger_event() {
  local event=$1
  local callback=$2

  shift
  shift
  local code=$callback
  i=1
  for var in "$@"; do
    code=$code" \"\${$i}\""
    let i++
  done
  if [[ "$(type -t $callback)" == "function" ]]; then
    eval "$code" || return 1
  fi
  return 0
}

##
# Helper to echo a table-like output.
#
function _cloudy_echo_aligned_columns() {
  parse_args "$@"
  local lpad=${parse_args__options__lpad:-1}
  local rpad=${parse_args__options__rpad:-4}
  local lborder="${parse_args__options__lborder}"
  local mborder="${parse_args__options__mborder}"
  local rborder="${parse_args__options__rborder}"
  local top="${parse_args__options__top}"

  # Draw a line
  local width=${#lborder}
  local column_width
  local line

  i=0
  for column_width in "${_cloudy_table_col_widths[@]}"; do
    width=$(($width + $lpad + $column_width + $rpad))
    if [[ $i -lt ${#_cloudy_table_col_widths} ]]; then
      width=$(($width + ${#mborder}))
    fi
    let i++
  done
  if [ ${#_cloudy_table_col_widths[@]} -gt 1 ]; then
    width=$(($width + ${#rborder}))
  fi

  if [[ "$top" ]]; then
    line="$(string_repeat "$top" $width)"
    echo "$line"
  fi

  # Deal with header
  if [ ${#_cloudy_table_header[@]} -gt 0 ]; then
    array_join__array=("${_cloudy_table_header[@]}")
    _cloudy_table_rows=("$(array_join '|')" "${_cloudy_table_rows[@]}")
  fi

  # Output the body
  local row_id=0
  for string_split__string in "${_cloudy_table_rows[@]}"; do
    string_split '|'
    local column_index=0
    echo -n "${lborder}"
    local last_column=${#_cloudy_table_col_widths[@]}
    let last_column--
    for cell in "${string_split__array[@]}"; do
      echo -n "$(string_repeat " " $lpad)$cell"
      echo -n "$(string_repeat " " $((${_cloudy_table_col_widths[$column_index]} - ${#cell} + $rpad)))"

      if [ $column_index -eq $last_column ]; then
        echo -n "${rborder}"
      else
        echo -n "${mborder}"
      fi
      let column_index++
    done
    echo

    if [ ${#_cloudy_table_header[@]} -gt 0 ] && [ $row_id -eq 0 ]; then
      echo $line
    fi

    let row_id++
  done

  echo "$line"

  # Reset the table global vars.
  _cloudy_table_col_widths=()
  _cloudy_table_header=()
  _cloudy_table_rows=()
}

function _cloudy_validate_command() {
  local command=$1

  local commands

  # See if it's a master command.
  eval $(get_config_keys "commands")
  array_has_value__array=(${commands[@]})
  array_has_value "$command" && return 0

  # Look for command as an alias.
  for c in "${commands[@]}"; do
    eval $(get_config_as -a "aliases" "commands.$c.aliases")
    array_has_value__array=(${aliases[@]})
    array_has_value "$command" && return 0
  done

  fail_because "You have called $(basename $SCRIPT) using the command \"$command\", which does not exist."
  return 1
}

function _cloudy_validate_command_arguments() {
  local command=$1

  command=$(_cloudy_get_master_command $command)
  local argument_keys
  local index=1
  local status=0
  local key
  local entered_value

  # See if it's a master command.
  eval $(get_config_keys_as "argument_keys" -a "commands.$command.arguments")
  for key in "${argument_keys[@]}"; do
    eval $(get_config_as "required" "commands.$command.arguments.$key.required")
    entered_value=$(eval "echo \${CLOUDY_ARGS[$index]}")
    [[ "$required" == true ]] && [[ ! "$entered_value" ]] && fail_because "Please provide <$key>." && status=1
    let index++
  done

  return $status
}

##
# Validate command input against the script's schema.
#
function _cloudy_validate_input_against_schema() {
  local config_path_to_schema=$1
  local name=$2
  local value=$3

  local errors
  echo $("$CLOUDY_PHP" $CLOUDY_ROOT/php/validate_against_schema.php "$CLOUDY_CONFIG_JSON" "$config_path_to_schema" "$name" "$value")
  return $?
}
