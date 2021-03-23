<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author: Jalu Ahmad Pambudi
 * @email: j.a.pambudi@gmail.com
 * @github: jonlord1012
 * @instagram: @j.a.pambudi
*/

Class Native_Session
{
    public $sess_name;
    public $sess_expire_time;
    public $userdata;

    public function __construct()
    {
        // Load CI
        $CI =& get_instance();

        // Set session name
        $this->sess_name = $CI->config->item('sess_cookie_name');

        // Set session expire
        $this->sess_expire_time = $CI->config->item('sess_expiration');
        
        #$this->userdata = array('userdata'=>'');

        // Init Session
        session_start();

        // Verify Session Part 1
        if(isset($_SESSION[$this->sess_name]) && $_SESSION[$this->sess_name])
        {
            if(!$this->verify_expire_time())
                $this->destroy();
            else
            {
                // Verify Keep flash
                if(isset($_SESSION[$this->sess_name]['keep_flash']) && $_SESSION[$this->sess_name]['keep_flash'])
                    $_SESSION[$this->sess_name]['keep_flash'] = false;
                else if(isset($_SESSION[$this->sess_name]['flashdata']) && $_SESSION[$this->sess_name]['flashdata'])
                    $this->destroy_flashdata();
            }
        }

        // Verify Session Part 2
        if((!isset($_SESSION[$this->sess_name]) || (!$_SESSION[$this->sess_name]) || (!$_SESSION[$this->userdata])))
        {
            $_SESSION[$this->sess_name] = array();
            $_SESSION[$this->userdata] = array();
            $this->userdata = array();
            // Add expire time
            $_SESSION[$this->sess_name]['expire'] = time() + $this->sess_expire_time;
            // Flashdata
            $_SESSION[$this->sess_name]['flashdata'] = array();
            // Keep Flash
            $_SESSION[$this->sess_name]['keep_flash'] = false;
        }
        
        // Register to $userdata
		  // this part give compatibility to old method of cookie 
		  // and give us tricky bypass to SameSite procedures...
		  $this->userdata = $_SESSION ;
    }

    // VERIFY EXPIRE TIME
    private function verify_expire_time()
    {
        if((time() - $_SESSION[$this->sess_name]['expire']) < $this->sess_expire_time)
            return true;
        else
            return false;
    }
    // SET SESSION
	// ------------------------------------------------------------------------

	/**
	 * Set userdata
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	mixed	$data	Session data key or an associative array
	 * @param	mixed	$value	Value to store
	 * @return	void
	 */
	public function set_userdata($data, $value = NULL)
	{
		if (is_array($data))
		{
			foreach ($data as $key => &$value)
			{
				$_SESSION[$key] = $value;
				#$this->sess_name[$key] = $value;
			}

			return;
		}

		$_SESSION[$data] = $value;
		#$this->sess_name[$data] = $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Userdata (fetch)
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	string	$key	Session data key
	 * @return	mixed	Session data value or NULL if not found
	 */
	public function userdata($key = NULL)
	{
		if (isset($key))
		{
		   #return isset($this->sess_name[$key]) ? $this->sess_name['$key'] : NULL ;
			return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
		}
		elseif (empty($_SESSION))
		{
			return array();
		}

		$userdata = array();
		$_exclude = array_merge(
			array($this->sess_name),
			$this->get_flash_keys(),
			$this->get_temp_keys()
		);

		foreach (array_keys($_SESSION) as $key)
		#foreach (array_keys($this->sess_name) as $key)
		{
			if ( ! in_array($key, $_exclude, TRUE))
			{
				$userdata[$key] = $_SESSION[$key];
				#$userdata[$key] = $this->sess_name[$key];
			}
		}

		return $userdata;
	}

    
    // SET SESSION
    public function set($key, $value = null)
    {
        if($key)
        {
            // Verify if is an array
            if(is_array($key))
                $this->_setarray($key);
            else
                $_SESSION[$this->sess_name][$key] = $value;

            return true;
        }
        else
            return null;
    }

    // SET SESSION ARRAY
    private function _setarray($data)
    {
        foreach ($data as $key => $value)
            $_SESSION[$this->sess_name][$key] = $value;
    }

    // GET A SESSION INSTANCE
    public function get($key)
    {
        if(isset($_SESSION[$this->sess_name][$key]))
            return $_SESSION[$this->sess_name][$key];
        else
            return null;
    }

    // GET ALL SESSION
    public function all_session()
    {
        if($this->verify_expire_time())
            return $_SESSION[$this->sess_name];
        else
        {
            $this->destroy();
            return false;
        }
    }
   
    // DESTROY SESSION
    public function sess_destroy()
    {
        session_destroy();
    }

    // DESTROY SESSION
    public function destroy()
    {
        session_destroy();
    }

    /*
        FLASH DATA
     */
    
    // SET FLASH DATA
    public function set_flashdata($key, $value = null)
    {
        if($key)
        {
            // Verify if it is an array
            if(is_array($key))
                $this->_setflashdataarray($key);
            else
                $_SESSION[$this->sess_name]['flashdata'][$key] = $value;

            $this->keep_flashdata();

            return true;
        }
        else
            return null;
    }


    // GET AN FLASH DATA INSTANCE
    public function get_flashdata($key)
    {
        if(isset($_SESSION[$this->sess_name]['flashdata'][$key]))
            return $_SESSION[$this->sess_name]['flashdata'][$key];
        else
            return null;
    }

    // KEEP FLASH DATA
    public function keep_flashdata()
    {
        $_SESSION[$this->sess_name]['keep_flash'] = true;
    }

    // SET FLASH DATA ARRAY
    private function _setflashdataarray($data)
    {
        foreach ($data as $key => $value)
            $_SESSION[$this->sess_name]['flashdata'][$key] = $value;
    }

    // DESTROY FLASH DATA
    public function destroy_flashdata()
    {
        $_SESSION[$this->sess_name]['flashdata'] = array();
        $_SESSION[$this->sess_name]['keep_flash'] = false;
    }
    

	/**
	 * Unset userdata
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	mixed	$key	Session data key(s)
	 * @return	void
	 */
	public function unset_userdata($key)
	{
		if (is_array($key))
		{
			foreach ($key as $k)
			{
				unset($_SESSION[$k]);
				#unset($this->sess_name[$k]);
			}

			return;
		}

		unset($_SESSION[$key]);
		#unset($this->sess_name[$key]);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get flash keys
	 *
	 * @return	array
	 */
	public function get_flash_keys()
	{
		if ( ! isset($_SESSION[$this->sess_name]))
		{
			return array();
		}

		$keys = array();
		foreach (array_keys($_SESSION[$this->sess_name]) as $key)
		{
			is_int($_SESSION[$this->sess_name][$key]) OR $keys[] = $key;
		}

		return $keys;
	}

	/**
	 * Get temp keys
	 *
	 * @return	array
	 */
	public function get_temp_keys()
	{
		if ( ! isset($_SESSION[$this->sess_name]))
		{
			return array();
		}

		$keys = array();
		foreach (array_keys($_SESSION[$this->sess_name]) as $key)
		{
			is_int($_SESSION[$this->sess_name][$key]) && $keys[] = $key;
		}

		return $keys;
	}

	/**
	 * Flashdata (fetch)
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	string	$key	Session data key
	 * @return	mixed	Session data value or NULL if not found
	 */
	public function flashdata($key = NULL)
	{
		if (isset($key))
		{
			return (isset($_SESSION[$this->sess_name], $_SESSION[$this->sess_name][$key], $_SESSION[$key]) && ! is_int($_SESSION[$this->sess_name][$key]))
				? $_SESSION[$key]
				: NULL;
		}

		$flashdata = array();

		if ( ! empty($_SESSION[$this->sess_name]))
		{
			foreach ($_SESSION[$this->sess_name] as $key => &$value)
			{
				is_int($value) OR $flashdata[$key] = $_SESSION[$key];
			}
		}

		return $flashdata;
	}

}

/* End of file Native_Session.php */
