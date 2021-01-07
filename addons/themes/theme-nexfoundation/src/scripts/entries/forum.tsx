/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import "../../scss/custom.scss";
import React from "react";
import { Advertisement } from "../components/advertisement";
import { onContent } from "@vanilla/library/src/scripts/utility/appUtils";
import { mountReact } from "@vanilla/react-utils";
import ReactDOM from "react-dom";

onContent(() => {
    bootstrap();
    articleList();
});

function bootstrap() {
    const adElement = document.getElementById("nex-advertisement");
    if (adElement) {
        mountReact(<Advertisement />, adElement, undefined);
    }
}
function articleList() {
    var ItemDiscussionLength = document.getElementsByClassName("ItemDiscussion").length + 1;

    for (let i = 1; i < ItemDiscussionLength; i++) {
        var element = document.getElementById(`Discussion_${i}`);
        var json = {};
        json.data = JSON.parse(element.dataset.meta).tags;
        json.id = i;

        let menuItems = [];
        for (var g = 0; g < json.data.length; g++) {
            menuItems.push(<div class="tag">{`#${json.data[g].name}`}</div>);
        }

        var adElement = document.getElementById(`tag_${i}`);
        if (adElement) {
            mountReact(
                <div>
                    <div class="tagBlock">{menuItems}</div>
                </div>,
                adElement,
                undefined,
            );
        }
    }
}