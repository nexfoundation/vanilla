/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import "../../scss/custom.scss";
import React from "react";
import { Advertisement } from "../components/advertisement";
import { onContent } from "@vanilla/library/src/scripts/utility/appUtils";
import { mountReact } from "@vanilla/react-utils";

interface ITagsData {
    [key: string]: any;
}

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
    let ItemDiscussionLength = document.getElementsByClassName("ItemDiscussion").length + 1;

    for (let i = 1; i < ItemDiscussionLength; i++) {
        let element = document.getElementById(`Discussion_${i}`);

        let json: ITagsData = {};
        json.data = JSON.parse(element!.dataset.meta!).tags;
        json.id = i;

        let menuItems: any[] = [];
        for (let g = 0; g < json.data.length; g++) {
            menuItems.push(<div className="tag">{`#${json.data[g].name}`}</div>);
        }

        let adElement = document.getElementById(`tag_${i}`);
        if (adElement) {
            mountReact(
                <div>
                    <div className="tagBlock">{menuItems}</div>
                </div>,
                adElement,
                undefined,
            );
        }
    }
}
