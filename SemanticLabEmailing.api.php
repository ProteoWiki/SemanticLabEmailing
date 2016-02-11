<?php

class ApiSemanticLabEmailing extends ApiBase {

	public function execute() {
		$params = $this->extractRequestParams();

		$emailing = $params['emailing'];
		$target = $params['target'];
		$method = $params['method'];
		$values = $params['values'];

		$output = SemanticLabEmailingPageCreator::actOnPage( $emailing, $target, $method, $values );

		$this->getResult()->addValue( null, $this->getModuleName(), array ( 'status' => $output ) );

		return true;
	}

	public function getAllowedParams() {
		return array(
			'emailing' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'method' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'target' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'values' => array(
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
			'target' => 'Affected page',
			'values' => 'Given values'
		);
	}
	public function getVersion() {
		return __CLASS__ . ': 1.1';
	}

}