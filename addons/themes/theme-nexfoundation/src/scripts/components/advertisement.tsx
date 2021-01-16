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
                <div className="hot-forum">熱門看板</div>
                <div className="hot-forum-root">
                    <div className="hot-forum-root_topic">簽證</div>
                    <div className="hot-forum-root_topic">學校</div>
                    <div className="hot-forum-root_topic">職涯</div>
                    <div className="hot-forum-root_topic">生活</div>
                    <div className="hot-forum-root_topic">心情</div>
                    <div className="hot-forum-root_topic">新聞</div>
                </div>
                <div className="hot-tags">熱門標籤</div>
                <div></div>
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
