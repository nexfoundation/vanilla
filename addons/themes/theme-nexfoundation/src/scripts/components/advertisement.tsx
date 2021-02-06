/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import React, { Fragment } from "react";
import gdn from "@library/gdn";

export class Advertisement extends React.Component {
    open = () => {
        window.open("https://www.nexf.org/");
    };

    render() {
        return (
            <Fragment>
                <img
                    className="banner"
                    src={`${gdn.meta.currentThemePath}/assets/ad.svg`}
                    alt="nex foundation"
                    onClick={() => this.open()}
                />
            </Fragment>
        );
    }
}
