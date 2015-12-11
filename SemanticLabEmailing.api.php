<?php

class SemanticLabEmailing extends ApiBase {

	public function execute() {
		$params = $this->extractRequestParams();

		return true;
	}

	public function getAllowedParams() {
		return array(
			'emailing' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'target' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			)
		);
	}
	public function getDescription() {
		return array(
			'API for voting satisfaction'
		);
	}
	public function getParamDescription() {
		return array(
			'emailing' => 'Type of emailing',
			'target' => 'Affected page'
		);
	}
	public function getVersion() {
		return __CLASS__ . ': 1.1';
	}

}