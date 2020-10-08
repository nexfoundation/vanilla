/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import React, { Fragment } from "react";
import gdn from "@library/gdn";

export class Advertisement extends React.Component {
    render() {
        return (
            <Fragment>
                <img className="banner" src={`${gdn.meta.currentThemePath}/assets/ad.png`} alt="nex foundation" />
                <div className="text">
                    <div className="subTitle">如果流浪是為了找回家的路</div>
                    <div className="subTitle">我們可以把回家的路變得更美好</div>
                    <div className="more">了解更多關於NEX</div>
                </div>
            </Fragment>
        );
    }
}
