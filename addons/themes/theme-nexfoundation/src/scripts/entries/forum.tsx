/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import "../../scss/custom.scss";
import React from 'react';
import * as ReactDOM from 'react-dom';
import { Advertisement } from "../components/advertisement"

window.onload = function () {
  ReactDOM.render(<Advertisement />, document.getElementById('nex-advertisement'));
};
