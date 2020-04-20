import React, { useState, useEffect } from 'react'

import Keywords from '../shared/Keywords'
import GroupSummary from '../shared/GroupSummary'
import Users from '../shared/Users'
import DateSelect from '../shared/DateSelect'
import axios from 'axios'
import { Heading } from 'evergreen-ui'
import { QueryToObject } from '../formatters'
import urls from '../config/urls'
import GroupSummaryToEntity from '../mappings/GroupSummaryToEntity' 

export default (props) => {

    const { data } = props;

    const [comments_verbal_portrait, set_comments_verbal_portrait] = useState(data.comments_verbal_portrait);
    const [posts_verbal_portrait, set_posts_verbal_portrait] = useState(data.posts_verbal_portrait);
    const [group_summary, set_group_summary] = useState(data.summary);
    const [top_commentators, set_top_commentators] = useState([]);

    useEffect(() => {
        var currentQuery = QueryToObject();
        var data = {};

        data.id = currentQuery.id;
        var query = Object.keys(data)
            .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k]))
            .join('&');

        console.log(query);

        getGroupCommentsVP(query)
        getGroupPostsVP(query)
        getGroupSummary(query)
        getGroupTopCommentators(query)
    }, [])

    const getGroupCommentsVP = (query) => {
        axios.get(`${urls.groupVerbalPortraitUrl}?${query}`)
            .then(res => {
                if (res.data.result === 'success') {
                    set_comments_verbal_portrait(res.data.payload);
                } else {
                    console.error(res);
                }
            })
            .catch(err => {
                console.log(err);
            })
    }

    const getGroupSummary = (query) => {
        axios.get(`${urls.groupSummaryUrl}?${query}`)
            .then(res => {
                if (res.data.result === 'success') {
                    set_group_summary(GroupSummaryToEntity(res.data.payload))
                } else {
                    console.log(res.data);
                }
            })
            .catch(e => {
                console.log(e);
            })
    }

    const getGroupPostsVP = (query) => {
        axios.get(`${urls.groupPostsVerbalPortraitUrl}?${query}`)
            .then(res => {
                if (res.data.result === 'success') {
                    set_posts_verbal_portrait(res.data.payload)
                } else {
                    console.log(res.data);
                }
            })
            .catch(e => {
                console.log(e);
            })
    }

    const getGroupTopCommentators = (query) => {
        axios.get(`${urls.groupTopCommentatorsUrl}?${query}`)
        .then(res => {
            if (res.data.result === 'success') {
                set_top_commentators(res.data.payload)
            } else {
                console.log(res.data);
            }
        })
        .catch(e => {
            console.log(e);
        })
    }

    const dateChange = async (data) => {

        const currentQuery = QueryToObject();

        data.id = currentQuery.id;

        var query = Object.keys(data)
            .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k]))
            .join('&');

        getGroupCommentsVP(query)
        getGroupPostsVP(query)
        getGroupSummary(query)
        getGroupTopCommentators(query)
    }

    return <div>
        <div style={{ zIndex: 2, position: 'relative' }} className="block">
            <DateSelect onChange={dateChange} />
        </div>
        <div className="block">
            <Heading marginBottom={16} size={400} marginTop="default">Общие данные</Heading>
            <GroupSummary data={group_summary}/>
        </div>
        <div className="block">
            <Heading marginBottom={16} size={400} marginTop="default">Топ комментаторов</Heading>
            <Users 
                users={top_commentators}
                withKeywords={true} 
            />
        </div>
        <div className="block">
            <Heading marginBottom={16} size={400} marginTop="default">Словесный портрет постов</Heading>
            <Keywords data={posts_verbal_portrait} />
        </div>
        <div className="block">
            <Heading marginBottom={16} size={400} marginTop="default">Словесный портрет комментариев</Heading>
            <Keywords data={comments_verbal_portrait} />
        </div>
    </div>
}