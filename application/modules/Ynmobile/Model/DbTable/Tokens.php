<?php

class Ynmobile_Model_DbTable_Tokens extends Engine_Db_Table
{
	protected $_name = 'ynmobile_tokens';
	protected $_rowClass = 'Ynmobile_Model_Token';

	/**
	 * @var int Token length.
	 */
	CONST TOKEN_LEN = 24;

	/**
	 * Get token by user.
	 * @return array|null
	 */
	function getTokenByUserId($iUser)
	{
		$select = $this -> select()  -> where('user_id = ?', $iUser) -> limit(1);
		return $this->fetchRow($select);
	}

	/**
	 * Add token by $iUserId
	 *
	 * @global string $token
	 * @param array $aUser
	 * @return array
	 */
	function createToken($oUser)
	{
		global $token;
		/**
		 * @var int
		 */
		$iUserId = intval($oUser->user_id);

		if ($token)
		{
			// delete old token
			$this->deleteToken($token);
		}

		//refine token
		$coreApi = new Ynmobile_Api_Core();
		$token = $coreApi -> getRandomString(self::TOKEN_LEN);

		// insert new token
		$aInsert = array(
			'token_id' => $token,
			'user_id' => $iUserId,
			'created_at' => time(),
		);

		$this -> insert($aInsert);

		return $aInsert;
	}

	/**
	 * delete token by token id
	 * @param string $sToken
	 */
	function deleteToken($sToken)
	{
		$sWhere = "token_id='{$sToken}'";
		$this -> delete($sWhere);
	}

	/**
	 * Get token information.
	 *
	 * @param string $sToken
	 * @return array Token information.
	 */
	function getToken($sToken)
	{
		$sWhere = "token_id = '{$sToken}'";
		$select = $this -> select() -> where($sWhere)->limit(1);
		return $this->fetchRow($select);
	}

	/**
	 * If token is not valid, return an error message and error code.
	 *
	 * @param string $sToken
	 * @return array
	 */
	public function isValid($sToken)
	{
		/**
		 * @var array
		 */
		$aToken = $this -> getToken($sToken);

		if (!$aToken)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Token is not valid!")
			);
		}
		return array();
	}

}
