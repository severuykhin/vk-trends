import React from 'react'
import { Pane, Text, Strong } from 'evergreen-ui'
import GroupSummaryType from '../types/GroupSummary'

interface GroupSummaryProps {
    data: GroupSummaryType
}

export default (props: GroupSummaryProps) => {

    const { data } = props;

    return (
        <Pane clearfix>
            <Pane
                elevation={1}
                border
                width={150}
                height={80}
                marginRight={16}
                marginBottom={16}
                float="left"
                display="flex"
                justifyContent="center"
                alignItems="center"
                flexDirection="column">
                <Strong>{data.posts_count}</Strong>
                <Text size={300}>Постов</Text>
            </Pane>
            <Pane
                elevation={1}
                border
                width={150}
                height={80}
                marginRight={16}
                marginBottom={16}
                float="left"
                display="flex"
                justifyContent="center"
                alignItems="center"
                flexDirection="column">
                <Strong>{data.comments_count}</Strong>
                <Text size={300}>Комментариев</Text>
            </Pane>
            <Pane
                elevation={1}
                border
                width={150}
                height={80}
                marginRight={16}
                marginBottom={16}
                float="left"
                display="flex"
                justifyContent="center"
                alignItems="center"
                flexDirection="column">
                <Strong>{data.views_per_post}</Strong>
                <Text size={300}>Просмотров на пост</Text>
            </Pane>
            <Pane
                elevation={1}
                border
                width={150}
                height={80}
                marginRight={16}
                marginBottom={16}
                float="left"
                display="flex"
                justifyContent="center"
                alignItems="center"
                flexDirection="column">
                <Strong>{data.likes_per_post}</Strong>
                <Text size={300}>Лайков на пост</Text>
            </Pane>
            <Pane
                elevation={1}
                border
                width={150}
                height={80}
                marginRight={16}
                marginBottom={16}
                float="left"
                display="flex"
                justifyContent="center"
                alignItems="center"
                flexDirection="column">
                <Strong>{data.comments_per_post}</Strong>
                <Text size={300}>Комментариев на пост</Text>
            </Pane>
            <Pane
                elevation={1}
                border
                width={150}
                height={80}
                marginRight={16}
                marginBottom={16}
                float="left"
                display="flex"
                justifyContent="center"
                alignItems="center"
                flexDirection="column">
                <Strong>{data.reposts_per_post}</Strong>
                <Text size={300}>Репостов на пост</Text>
            </Pane>
        </Pane>
        
    );
}